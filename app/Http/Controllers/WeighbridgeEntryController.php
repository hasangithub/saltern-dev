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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class WeighbridgeEntryController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function index(Request $request)
    {
       $entries = WeighbridgeEntry::with(['owner', 'buyer', 'membership','receipt'])
        ->orderBy('transaction_date')
        ->orderBy('created_at')
        ->get();

    $grouped = $entries->groupBy(function ($item) {
        if ($item->transaction_date) {
            return Carbon::parse($item->transaction_date)->toDateString();
        }
        return 'unknown';
    });

    $numbered = collect();
    foreach ($grouped as $day => $entriesForDay) {
        foreach ($entriesForDay->values() as $index => $entry) {
            $entry->turn_no = $index + 1;
            $numbered->push($entry);
        }
    }

    return view('weighbridge_entries.index', ['entries' => $numbered]);
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

    public function store(Request $request)
    {
        $message = "entry created successfully for date " . now()->format('d-m-Y');
        $phone = '94713857269'; 

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

        $netWeight = $validated['tare_weight'] - $validated['initial_weight'];

        // Calculate number of bags (assuming each bag is 50kg)
        $bags = $netWeight / $bagPerWeight;

        // Calculate service charge
        $serviceChargeMain = $bags * $bagPrice;
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
                    'repayment_date' => now(),
                    'payment_method' => 'Cash',
                    'notes' => 'Loan Deducation',
                    'status' => 'pending',
                ]);

                $journal = JournalEntry::create([
                    'journal_date' => Carbon::now()->toDateString(), // YYYY-MM-DD
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
            'journal_date' => Carbon::now()->toDateString(), // YYYY-MM-DD
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
       ->route('weighbridge_entries.invoice', ['entry' => $entry->id])
       ->with('success', 'Entry created successfully. Printing invoice...');
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

            // Soft delete related loan repayments, if any
            OwnerLoanRepayment::where('weighbridge_entry_id', $entry->id)->delete();

            // Soft delete the weighbridge entry
            $entry->delete();

            return redirect()->route('weighbridge_entries.index')
                            ->with('success', 'Weighbridge entry and related loan repayment deleted.');
        }

        public function invoice(WeighbridgeEntry $entry)
        {
            $pdf = Pdf::loadView('weighbridge_entries.invoice', ['entry' => $entry, 'from_pdf' => true])
            ->setPaper('A6', 'portrait')
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => true,
                'isFontSubsettingEnabled' => true
            ]);// A6 in points
            return $pdf->stream("invoice_{$entry->id}.pdf");

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
