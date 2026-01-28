<?php

namespace App\Http\Controllers;

use App\Helpers\SettingsHelper;
use App\Models\Buyer;
use App\Models\JournalDetail;
use App\Models\JournalEntry;
use App\Models\Ledger;
use App\Models\Membership;
use App\Models\Owner;
use App\Models\Saltern;
use App\Models\Side;
use App\Models\WeighbridgeEntry;
use App\Models\Yahai;
use App\Models\OwnerLoan;
use App\Models\OwnerLoanRepayment;
use App\Models\PrivateWeighbridgeEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;

class WeighbridgeEntryController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function index(Request $request)
    {
        return view('weighbridge_entries.index');
    }

    public function data()
    {
        $entries = WeighbridgeEntry::with([
            'buyer', 'membership.owner', 'membership.saltern.yahai', 'loanRepayments', 'receipt'
        ])->orderBy('id', 'desc');
    
        return DataTables::eloquent($entries)
            ->addColumn('turn_no', fn($entry) => optional($entry)->turn_no)
            ->addColumn('buyer_name', fn($entry) => optional($entry->buyer)->full_name)
            ->filterColumn('buyer_name', function ($query, $keyword) {
                $query->whereHas('buyer', function ($q) use ($keyword) {
                    $q->where('full_name', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('owner_name', fn($entry) => optional($entry->membership->owner)->name_with_initial)
            ->filterColumn('owner_name', function ($query, $keyword) {
                $query->whereHas('membership.owner', function ($q) use ($keyword) {
                    $q->where('name_with_initial', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('yahai_name', fn($entry) => optional($entry->membership->saltern->yahai)->name)
            ->filterColumn('yahai_name', function ($query, $keyword) {
                $query->whereHas('membership.saltern.yahai', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('waikal', fn($entry)     => optional($entry->membership->saltern)->name)
            ->filterColumn('waikal', function ($query, $keyword) {
                $query->whereHas('membership.saltern', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('net_weight', fn($entry) => $entry->net_weight)
            ->addColumn('bags', fn($entry) => $entry->bags_count)
            ->addColumn('amount', fn($entry) => number_format($entry->total_amount, 2))
            ->addColumn('receipt', function ($entry) {
                if ($entry->receipt) {
                    return '<a href="' . route('receipts.show', $entry->receipt->id) . '" target="_blank">
                                <span class="badge bg-success">#' . $entry->receipt->id . '</span>
                            </a>';
                } else {
                    return '<span class="badge bg-warning">No</span>';
                }
            })
            ->addColumn('loan', fn($entry) =>  number_format($entry->loanRepayments->sum('amount'),2))
            ->addColumn('action', fn($entry) => view('partials.actions', compact('entry'))->render())
            ->rawColumns(['receipt', 'action'])
            ->make(true);
    }

    public function create()
    {
        $sides  = Side::all();
        $owners = Owner::all();
        $buyers = Buyer::all();
        $ledgers = Ledger::all();
        $memberships = Membership::all();
        $nextSerialNo = WeighbridgeEntry::max('id') + 1;

        return view('weighbridge_entries.create', compact('owners', 'buyers', 'memberships', 'nextSerialNo', 'sides', 'ledgers'));
    }

    public function initialCreate()
    {
        $sides  = Side::all();
        $owners = Owner::all();
        $buyers = Buyer::all();
        $ledgers = Ledger::all();
        $memberships = Membership::all();
        $nextSerialNo = WeighbridgeEntry::max('id') + 1;
        $pendingCount = WeighbridgeEntry::where('status', 'approved')
        ->whereNull('tare_weight')
        ->count();
        $privateWeighPendingCount = PrivateWeighbridgeEntry::where('status', 'completed')
    ->where(function ($q) {
        $q->whereNull('second_weight')
          ->orWhere('second_weight', 0);
    })
    ->count();

        return view('weighbridge_entries.initial_create', compact('owners', 'buyers', 'memberships', 'nextSerialNo', 'sides', 'ledgers', 'pendingCount', 'privateWeighPendingCount'));
    }

    public function initialStore(Request $request)
    {
        $validated =  $request->validate([
            'vehicle_id' => 'required|string',
            'culture' => 'required|string',
            'initial_weight' => 'required|numeric',
            'transaction_date' => 'nullable|date',
            'membership_id' => 'required|exists:memberships,id',
            'buyer_id' => 'required|exists:buyers,id',
        ]);

        $initialWeight =  $validated['initial_weight'];
        $membership = Membership::findOrFail($request->membership_id);
        $buyer = Buyer::findOrFail($request->buyer_id);
        $data = $request->all();
        $bagPrice = SettingsHelper::get('bag_price', 100); // default 100 if not set
        $bagPerWeight = SettingsHelper::get('bag_per_weight', 50);
        $data['owner_id'] = $membership->owner_id;
        $data['transaction_date'] = $validated['transaction_date'] ?? date("Y-m-d");
        $data['bag_price'] = $bagPrice;
        $data['status'] = 'approved';
        $data['first_weight_time'] = now()->format('H:i:s');
 
         
        $entry = WeighbridgeEntry::create($data);
        $weighbridgeEntryId = $entry->id;

        $repayments = $request->input('repayments'); // Array: loan_id => amount

        $waikal =   $membership->saltern->yahai->name." ".$membership->saltern->name;
       
        $ownerPhone = $membership->owner->phone_number;
        $buyerPhone = $buyer->phone_number;
        $todayDate = date('Y-m-d');
        $vehicleNumber = $validated['vehicle_id'];

        $smsCommon = "{$todayDate}\n"
        . "{$membership->owner->name_with_initial}\n"
        . "{$waikal}\n"
        . "{$buyer->full_name}\n"
        . "Ready to load.";
        // . "Vehicle No: {$vehicleNumber}\n"
        
        $this->smsService->sendSms($ownerPhone, $smsCommon);
       
       // return redirect()->route('weighbridge_entries.index')->with('success', 'Weighbridge entry created successfully.');
       return redirect()
       ->route('weighbridge.initial.create')
       ->with('success', 'First weight created successfully. Printing...')
       ->with('print_type', 'first')
       ->with('print_entry_id', $entry->id);
    }

    public function finalEdit($id)
    {
        $data = WeighbridgeEntry::with(['membership'])->findOrFail($id);
        $yahaiId = $data->membership->saltern->yahai_id;
        $sideId  = $data->membership->saltern->yahai->side_id;
        $salternId = $data->membership->saltern_id;

        $sides  = Side::all();
        $yahais =  Yahai::where('side_id', $sideId)->get();
        $salterns = Saltern::where('yahai_id', $yahaiId)->get();
        $owners = Owner::all();
        $buyers = Buyer::all();
        $ledgers = Ledger::all();
        $memberships = Membership::all();
        $nextSerialNo = WeighbridgeEntry::max('id') + 1;

        return view('weighbridge_entries.final_edit', compact('owners', 'buyers', 'memberships', 'nextSerialNo', 'sides', 'ledgers','data', 'yahais', 'salterns'));
    }

    public function finalUpdate(Request $request, $entryId)
    {
        $entry = WeighbridgeEntry::findOrFail($entryId);

        $validated =  $request->validate([
            'vehicle_id' => 'required|string',
            'culture' => 'required|string',
            'initial_weight' => 'required|numeric',
            'tare_weight' => 'required|numeric|min:0|gte:initial_weight',
            'transaction_date' => 'nullable|date',
            'membership_id' => 'required|exists:memberships,id',
            'buyer_id' => 'required|exists:buyers,id',
        ]);

        $membership = Membership::findOrFail($request->membership_id);
        $buyer = Buyer::findOrFail($request->buyer_id);
        $data = $request->all();
        $bagPrice = SettingsHelper::get('bag_price', 100); // default 100 if not set
        $bagPerWeight = SettingsHelper::get('bag_per_weight', 50);
        $ownerSharePercentage = SettingsHelper::get('owner_share_percentage', 30);

        $entry->net_weight = bcsub($validated['tare_weight'], $entry->initial_weight, 2);
        $entry->bags_count = bcdiv($entry->net_weight, $bagPerWeight, 2); // Divide net_weight by 50, keep 2 decimal places
        $entry->total_amount = bcmul($entry->bags_count, $bagPrice, 2);
        $entry->owner_share_percentage = $ownerSharePercentage;

        $entry->vehicle_id    = $validated['vehicle_id'];
        $entry->culture       = $validated['culture'];
        $entry->tare_weight   = $validated['tare_weight'];
        $entry->owner_id      = $membership->owner_id;
        $entry->buyer_id      = $validated['buyer_id'];
        $entry->membership_id = $validated['membership_id'];
        $entry->bag_price     = $bagPrice;
        $entry->status        = 'approved';
        $entry->second_weight_time = now()->format('H:i:s');

        $netWeight = $validated['tare_weight'] - $validated['initial_weight'];

        // Calculate number of bags (assuming each bag is 50kg)
        $bags = $entry->bags_count;

        // Calculate service charge
        $serviceChargeMain = $entry->total_amount;
        $loans = OwnerLoan::where('membership_id', $request->membership_id)
            ->whereRaw('(approved_amount - (SELECT COALESCE(SUM(amount), 0) FROM owner_loan_repayments WHERE owner_loan_repayments.owner_loan_id = owner_loans.id)) > 0')
            ->orderBy('created_at', 'asc')
            ->get();

        $entry->save();

        $weighbridgeEntryId = $entryId;

        $repayments = $request->input('repayments'); // Array: loan_id => amount

        $totalPaidNow = 0;

        foreach ($repayments  ?? [] as $loanId => $amount) {
            if (!empty($amount) && $amount > 0) {

                $totalPaidNow += $amount;

                OwnerLoanRepayment::create([
                    'owner_loan_id' => $loanId,
                    'buyer_id' => $validated['buyer_id'],
                    'weighbridge_entry_id' => $weighbridgeEntryId,
                    'amount' => $amount,
                    'repayment_date' => $validated['transaction_date'] ?? date("Y-m-d"),
                    'payment_method' => 'Cash',
                    'notes' => 'Loan Deducation',
                    'status' => 'pending',
                ]);

                $journal = JournalEntry::create([
                    'journal_date' => $validated['transaction_date'] ?? date("Y-m-d"),
                    'description' => 'Loan deduction for weighbridge entry#'.$weighbridgeEntryId." LoanId#".$loanId,
                ]);

                $details = [
                    [
                        'journal_id' => $journal->id,
                        'ledger_id' => 10,
                        'sub_ledger_id' => 100,
                        'debit_amount' => $amount,
                        'credit_amount' => null,
                        'description' => '',
                    ],
                    [
                        'journal_id' => $journal->id,
                        'ledger_id' => 12,
                        'sub_ledger_id' => 115,
                        'debit_amount' => null,
                        'credit_amount' => $amount,
                        'description' => '',
                    ],
                ];

                JournalDetail::insert($details);
            }
        }

       

        $journal = JournalEntry::create([
            'journal_date' => $validated['transaction_date'] ?? date("Y-m-d"),
            'description' => 'Service Charge Entry - MembershipId#'.$membership->id." weighbridgeEntryId#".$weighbridgeEntryId,
        ]);

        $details = [
            [
                'journal_id' => $journal->id,
                'ledger_id' => 10,  // e.g. Buyer
                'sub_ledger_id' => 101,
                'debit_amount' => $serviceChargeMain,
                'credit_amount' => null,
                'description' => 'Service charge debited from buyer#'.$buyer->id,
            ],
            [
                'journal_id' => $journal->id,
                'ledger_id' => 151,  // e.g. ServiceCharge income
                'sub_ledger_id' => null,
                'debit_amount' => null,
                'credit_amount' => round($serviceChargeMain * 0.70, 2),
                'description' => 'Service charge income weighbridgeEntryId#'.$weighbridgeEntryId,
            ],
            [
                'journal_id' => $journal->id,
                'ledger_id' => 176,  // e.g. Owner share
                'sub_ledger_id' => null,
                'debit_amount' => null,
                'credit_amount' => round($serviceChargeMain * 0.30, 2),
                'description' => 'Owner share of service charge - MembershipId#'.$membership->id." weighbridgeEntryId#".$weighbridgeEntryId,
            ],
        ];

        // 3. Bulk insert details
        JournalDetail::insert($details);

        $allLoans = OwnerLoan::where('membership_id', $request->membership_id)->get();
        $totalOutstanding = 0;

        foreach ($allLoans as $loan) {
            $paid = $loan->ownerLoanRepayment->sum('amount');
            $balance = $loan->approved_amount - $paid;
            if ($balance > 0) {
                $totalOutstanding += $balance;
            }
        }

        $waikal =   $membership->saltern->yahai->name." ".$membership->saltern->name;
       
        $ownerPhone = $membership->owner->phone_number;
        $buyerPhone = $buyer->phone_number;
        $todayDate = date('Y-m-d');
        $vehicleNumber = $validated['vehicle_id'];

        $smsCommon = "{$todayDate}\n"
        . "{$membership->owner->name_with_initial}\n"
        . "{$waikal}\n"
        . "{$buyer->full_name}\n"
        . "{$netWeight}kg\n"
        . "{$bags} bags\n"
        . "Service Charge Rs. " . number_format(round($serviceChargeMain, 2), 2) . "\n";

        $buyerSms = "{$todayDate}\n"
        . "{$vehicleNumber}\n"
        . "{$membership->owner->name_with_initial}\n"
        . "{$waikal}\n"
        . "{$buyer->full_name}\n"
        . "{$netWeight}kg\n"
        . "{$bags} bags\n"
        . "Service Charge Rs. " . number_format(round($serviceChargeMain, 2), 2) . "\n";
        
        $ownerSms = $smsCommon . "\n30% Reserved Rs. " . number_format(round($serviceChargeMain * 0.30, 2),2);

        if ($totalPaidNow > 0) {
            $ownerSms .= "\nLoan Paid : Rs. " . number_format($totalPaidNow, 2)
            . "\nOutstanding Balance: Rs. " . number_format($totalOutstanding, 2);

            $buyerSms .= "\nLoan Paid: Rs. " . number_format($totalPaidNow, 2);
        }

            $this->smsService->sendSms($ownerPhone, $ownerSms);
        if (!empty($buyer->phone_number)) {
           $this->smsService->sendSms($buyerPhone, $buyerSms);
        }

       // return redirect()->route('weighbridge_entries.index')->with('success', 'Weighbridge entry created successfully.');
       return redirect()
       ->route('weighbridge.initial.create')
       ->with('success', 'Entry created successfully. Printing invoice...')
       ->with('print_type', 'second')
       ->with('print_entry_id', $entry->id);
    }

    public function store(Request $request)
    {
        $validated =  $request->validate([
            'vehicle_id' => 'required|string',
            'culture' => 'required|string',
            'initial_weight' => 'required|numeric',
            'tare_weight' => 'required|numeric|min:0|gte:initial_weight',
            'transaction_date' => 'nullable|date',
            'membership_id' => 'required|exists:memberships,id',
            'buyer_id' => 'required|exists:buyers,id',
        ]);

        $membership = Membership::findOrFail($request->membership_id);
        $buyer = Buyer::findOrFail($request->buyer_id);
        $data = $request->all();
        $bagPrice = SettingsHelper::get('bag_price', 100); // default 100 if not set
        $bagPerWeight = SettingsHelper::get('bag_per_weight', 50);
        $data['owner_id'] = $membership->owner_id;
        $data['transaction_date'] = $validated['transaction_date'] ?? date("Y-m-d");
        $data['bag_price'] = $bagPrice;
        $data['status'] = 'approved';
   
        $ownerSharePercentage = SettingsHelper::get('owner_share_percentage', 30);

        
        $data['net_weight'] = bcsub($validated['tare_weight'], $validated['initial_weight'], 2);
        $data['bags_count'] = bcdiv($data['net_weight'], $bagPerWeight, 2); // Divide net_weight by 50, keep 2 decimal places
        $data['total_amount'] = bcmul($data['bags_count'], $bagPrice, 2);
        $data['owner_share_percentage'] =  $ownerSharePercentage;

        $netWeight = $data['net_weight'];

        // Calculate number of bags (assuming each bag is 50kg)
        $bags =  $data['bags_count'];

        // Calculate service charge
        $serviceChargeMain = $data['total_amount'];
        $loans = OwnerLoan::where('membership_id', $request->membership_id)
            ->whereRaw('(approved_amount - (SELECT COALESCE(SUM(amount), 0) FROM owner_loan_repayments WHERE owner_loan_repayments.owner_loan_id = owner_loans.id)) > 0')
            ->orderBy('created_at', 'asc')
            ->get();

        $entry = WeighbridgeEntry::create($data);
        $weighbridgeEntryId = $entry->id;

        $repayments = $request->input('repayments'); // Array: loan_id => amount

        $totalPaidNow = 0;

        foreach ($repayments  ?? [] as $loanId => $amount) {
            if (!empty($amount) && $amount > 0) {

                $totalPaidNow += $amount;

                OwnerLoanRepayment::create([
                    'owner_loan_id' => $loanId,
                    'buyer_id' => $validated['buyer_id'],
                    'weighbridge_entry_id' => $weighbridgeEntryId,
                    'amount' => $amount,
                    'repayment_date' => $validated['transaction_date'] ?? date("Y-m-d"),
                    'payment_method' => 'Cash',
                    'notes' => 'Loan Deducation',
                    'status' => 'pending',
                ]);

                $journal = JournalEntry::create([
                    'journal_date' => $validated['transaction_date'] ?? date("Y-m-d"),
                    'description' => 'Loan deduction for weighbridge entry#'.$weighbridgeEntryId." LoanId#".$loanId,
                ]);

                $details = [
                    [
                        'journal_id' => $journal->id,
                        'ledger_id' => 10,
                        'sub_ledger_id' => 100,
                        'debit_amount' => $amount,
                        'credit_amount' => null,
                        'description' => '',
                    ],
                    [
                        'journal_id' => $journal->id,
                        'ledger_id' => 12,
                        'sub_ledger_id' => 115,
                        'debit_amount' => null,
                        'credit_amount' => $amount,
                        'description' => '',
                    ],
                ];

                JournalDetail::insert($details);
            }
        }

       

        $journal = JournalEntry::create([
            'journal_date' => $validated['transaction_date'] ?? date("Y-m-d"),
            'description' => 'Service Charge Entry - MembershipId#'.$membership->id." weighbridgeEntryId#".$weighbridgeEntryId,
        ]);

        $details = [
            [
                'journal_id' => $journal->id,
                'ledger_id' => 10,  // e.g. Buyer
                'sub_ledger_id' => 101,
                'debit_amount' => $serviceChargeMain,
                'credit_amount' => null,
                'description' => 'Service charge debited from buyer#'.$buyer->id,
            ],
            [
                'journal_id' => $journal->id,
                'ledger_id' => 151,  // e.g. ServiceCharge income
                'sub_ledger_id' => null,
                'debit_amount' => null,
                'credit_amount' => round($serviceChargeMain * 0.70, 2),
                'description' => 'Service charge income weighbridgeEntryId#'.$weighbridgeEntryId,
            ],
            [
                'journal_id' => $journal->id,
                'ledger_id' => 176,  // e.g. Owner share
                'sub_ledger_id' => null,
                'debit_amount' => null,
                'credit_amount' => round($serviceChargeMain * 0.30, 2),
                'description' => 'Owner share of service charge - MembershipId#'.$membership->id." weighbridgeEntryId#".$weighbridgeEntryId,
            ],
        ];

        // 3. Bulk insert details
        JournalDetail::insert($details);

        $allLoans = OwnerLoan::where('membership_id', $request->membership_id)->get();
        $totalOutstanding = 0;

        foreach ($allLoans as $loan) {
            $paid = $loan->ownerLoanRepayment->sum('amount');
            $balance = $loan->approved_amount - $paid;
            if ($balance > 0) {
                $totalOutstanding += $balance;
            }
        }

        $waikal =   $membership->saltern->yahai->name." ".$membership->saltern->name;
       
        $ownerPhone = $membership->owner->phone_number;
        $buyerPhone = $buyer->phone_number;
        $todayDate = date('Y-m-d');
        $vehicleNumber = $validated['vehicle_id'];

        $smsCommon = "{$todayDate}\n"
        . "{$membership->owner->name_with_initial}\n"
        . "{$waikal}\n"
        . "{$buyer->full_name}\n"
        . "{$netWeight}kg\n"
        . "{$bags} bags\n"
        . "Service Charge Rs. " . number_format(round($serviceChargeMain, 2), 2) . "\n";

        $buyerSms = "{$todayDate}\n"
        . "{$vehicleNumber}\n"
        . "{$membership->owner->name_with_initial}\n"
        . "{$waikal}\n"
        . "{$buyer->full_name}\n"
        . "{$netWeight}kg\n"
        . "{$bags} bags\n"
        . "Service Charge Rs. " . number_format(round($serviceChargeMain, 2), 2) . "\n";
        
        $ownerSms = $smsCommon . "\n30% Reserved Rs. " . number_format(round($serviceChargeMain * 0.30, 2),2);

        if ($totalPaidNow > 0) {
            $ownerSms .= "\nLoan Paid : Rs. " . number_format($totalPaidNow, 2)
            . "\nOutstanding Balance: Rs. " . number_format($totalOutstanding, 2);

            $buyerSms .= "\nLoan Paid: Rs. " . number_format($totalPaidNow, 2);
        }

            $this->smsService->sendSms($ownerPhone, $ownerSms);
        if (!empty($buyer->phone_number)) {
           $this->smsService->sendSms($buyerPhone, $buyerSms);
        }

       // return redirect()->route('weighbridge_entries.index')->with('success', 'Weighbridge entry created successfully.');
       return redirect()
       ->route('weighbridge_entries.create')
       ->with('success', 'Entry created successfully. Printing invoice...')
       ->with('print_entry_id', $entry->id);
    }

    public function show($id)
    {
        $weighbridgeEntry = WeighbridgeEntry::findOrFail($id);

        return view('weighbridge_entries.show', compact('weighbridgeEntry'));
    }

    public function addTare(Request $request, $id)
    {
        $WeighbridgeEntry = WeighbridgeEntry::findOrFail($id);

        // Validate and save the approved amount
        $request->validate([
            'tare_weight' => 'required|numeric|min:0|gte:' . $WeighbridgeEntry->initial_weight,
        ]);

        $WeighbridgeEntry->tare_weight   = $request->tare_weight;
        $WeighbridgeEntry->bag_price = 100;
        $WeighbridgeEntry->status = 'approved';
        $WeighbridgeEntry->save();

        return redirect()->route('weighbridge_entries.show', $WeighbridgeEntry->id)
            ->with('success', 'Tare weight updated successfully.');
    }

    public function getYahais(Request $request)
    {
        $yahais = Yahai::where('side_id', $request->side_id)->get();
        return response()->json(['yahais' => $yahais]);
    }

    public function getSalterns(Request $request)
    {
        $salterns = Saltern::where('yahai_id', $request->yahai_id)->get();
        return response()->json(['salterns' => $salterns]);
    }

    public function getMembershipDetails($saltern_id)
    {
        $membership = Membership::where('saltern_id', $saltern_id)
            ->where('is_active', 1)
            ->with('owner')  // Eager load the owner
            ->first();

        Log::info("Selected saltern_id: " . $saltern_id);

        if ($membership) {
            Log::info("Selected saltern_id: " . $saltern_id . " Owner" . $membership->owner->name_with_initial);
            return response()->json([
                'status' => 'success',
                'membership' => $membership,
                'owner' => $membership->owner,  // Include the owner details in the response
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'No membership found for this saltern'
        ]);
    }

    public function destroy($id)
        {
            $entry = WeighbridgeEntry::findOrFail($id);

            $entry->deleted_by = auth('web')->id();
            $entry->save();
            
            OwnerLoanRepayment::where('weighbridge_entry_id', $entry->id)->delete();

            // Soft delete the weighbridge entry
            $entry->delete();

            return redirect()->route('weighbridge_entries.index')
                            ->with('success', 'Weighbridge entry and related loan repayment deleted.');
        }

        public function invoice(WeighbridgeEntry $entry, Request $request)
        {
            $mode = $request->query('mode');
            $repayment = OwnerLoanRepayment::where('weighbridge_entry_id', $entry->id)->get();
            $totalPaid = $repayment->sum('amount');
            $pdf = Pdf::loadView('weighbridge_entries.invoice', ['entry' => $entry, 'from_pdf' => true,  'repayment' => $repayment, 'totalPaid' => $totalPaid, 'mode'=> $mode])
            ->setPaper('A6', 'portrait')
            ->setOptions([
                'defaultFont' => 'times',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => true,
                'isFontSubsettingEnabled' => true
            ]);// A6 in points
            return $pdf->stream("invoice_{$entry->id}.pdf");

            //return view('weighbridge_entries.invoice', compact('entry'));
        }

        public function invoicePrintFirst(WeighbridgeEntry $entry)
        {
            $repayment = OwnerLoanRepayment::where('weighbridge_entry_id', $entry->id)->get();
            $totalPaid = $repayment->sum('amount');
            $pdf = Pdf::loadView('weighbridge_entries.invoice_first', ['entry' => $entry, 'from_pdf' => true,  'repayment' => $repayment, 'totalPaid' => $totalPaid])
            ->setPaper('A6', 'portrait')
            ->setOptions([
                'defaultFont' => 'times',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => true,
                'isFontSubsettingEnabled' => true
            ]);// A6 in points
            return $pdf->stream("invoice_first{$entry->id}.pdf");

            //return view('weighbridge_entries.invoice', compact('entry'));
        }

        public function edit($id)
        {
            $entry = WeighbridgeEntry::with(['owner', 'buyer'])->findOrFail($id);
            $owners = Owner::all();
            $buyers = Buyer::all();

            return view('weighbridge_entries.edit', compact('entry', 'owners', 'buyers'));
        }

        public function update(Request $request, $id)
        {
            $request->validate([
                'vehicle_id' => 'required|string|max:100',
                'buyer_id' => 'required|exists:buyers,id',
            ]);

            $entry = WeighbridgeEntry::findOrFail($id);
            $entry->update($request->only([
                'vehicle_id',
                'buyer_id'
            ]));

            return redirect()->route('weighbridge_entries.index')->with('success', 'Entry updated successfully.');
        }

}
