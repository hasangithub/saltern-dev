<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buyer;
use App\Models\JournalDetail;
use App\Models\JournalEntry;
use App\Models\OtherIncome;
use App\Models\WeighbridgeEntry;
use App\Models\OwnerLoan;
use App\Models\OwnerLoanRepayment;
use App\Models\PaymentMethod;
use App\Models\Receipt;
use App\Models\ReceiptDetail;
use App\Models\SubLedger;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{

    public function index()
    {
        $receipts = Receipt::with(['buyer', 'createdBy', 'details', 'bank'])->get();
        return view('receipts.index', compact('receipts'));
    }

public function create(Request $request)
{
    $buyers = Buyer::all();
    $paymentMethods = PaymentMethod::all();
    $banks = SubLedger::where('ledger_id', 11)->get();
    $pendingServiceCharges = collect();
    $pendingLoanRepayments = collect();
    $pendingOtherIncomes = collect();
    $buyerName = '';
    $buyerId = '';

    if ($request->filled('buyer_id')) {
        $buyer = Buyer::find($request->buyer_id);
        if ($buyer) {
            $buyerName = $buyer->full_name;
            $buyerId = $buyer->id;
            
            // Get pending weighbridge service charges
            $pendingServiceCharges = WeighbridgeEntry::where('buyer_id', $buyer->id)->where('is_service_charge_paid', 0)
                ->get();

            // Get pending owner loans assigned to this buyer
            $pendingLoanRepayments = OwnerLoanRepayment::where('buyer_id', $buyer->id)->where('status', 'pending')->get();

            $pendingOtherIncomes = OtherIncome::where('buyer_id', $buyer->id)->where('status', 'pending')->get();
        }
    }

    return view('receipts.create', compact('buyers', 'pendingServiceCharges', 'pendingLoanRepayments', 'pendingOtherIncomes', 'buyerName', 'buyerId', 'paymentMethods', 'banks'));
}

public function store(Request $request)
{   
    $validated = $request->validate([
        'buyer_id' => 'required|exists:buyers,id',
        'service_entry_ids' => 'array',
        'repayment_ids' => 'array',
        'otherincome_ids' => 'array',
        'payment_method_id' => 'required|in:1,2',
        'cheque_no' => 'nullable|string|max:50',
        'cheque_date' => 'nullable',
        'bank_sub_ledger_id' => 'nullable'
    ]);
    
    // ✅ Custom manual check after Laravel validation
    if (
        empty($request->input('service_entry_ids')) &&
        empty($request->input('repayment_ids')) &&
        empty($request->input('otherincome_ids'))
    ) {
        return back()
            ->withInput()
            ->withErrors([
                'service_entry_ids' => 'You must select at least one payment item (service entry, repayment, or other income).'
            ]);
    }


    $buyerId = $request->input('buyer_id');
    $serviceEntryIds = $request->input('service_entry_ids', []);
    $repaymentIds = $request->input('repayment_ids', []);
    $otherIncomeIds = $request->input('otherincome_ids', []);
    $paymentMethod = $request->input('payment_method_id');

    $totalAmount = 0;

    // 1. Calculate total amount first
    foreach ($serviceEntryIds as $id) {
        $totalAmount += WeighbridgeEntry::find($id)?->total_amount ?? 0;
    }
    foreach ($repaymentIds as $id) {
        $totalAmount += OwnerLoanRepayment::find($id)?->amount ?? 0;
    }
    foreach ($otherIncomeIds as $id) {
        $totalAmount += OtherIncome::find($id)?->amount ?? 0;
    }

    $receipt = Receipt::create([
        'buyer_id'     => $buyerId,
        'payment_method_id' => $validated['payment_method_id'],
        'bank_sub_ledger_id' => $validated['bank_sub_ledger_id'] ?? null,
        'cheque_no' => $validated['cheque_no'] ?? null,
        'cheque_date' => $validated['cheque_date'] ?? null,
        'receipt_date' => now(),
        'total_amount' => $totalAmount,
        'created_by'   => auth('web')->id(),
    ]);

    // Update service entries status
    $totalServiceCharge = 0;
    if (!empty($serviceEntryIds)) {

        $journal = JournalEntry::create([
            'journal_date' => Carbon::now()->toDateString(), // YYYY-MM-DD
            'description' => 'Receipt Service Charge Receipt#'.$receipt->id,
        ]);

        foreach($serviceEntryIds as $serviceEntryId) {
        
            $serviceCharge = WeighbridgeEntry::find($serviceEntryId)?->total_amount;
            $totalServiceCharge += $serviceCharge;

            ReceiptDetail::create([
                'receipt_id' => $receipt->id,
                'entry_type' => 'weighbridge',
                'entry_id'   => $serviceEntryId,
                'amount'     => $serviceCharge,
            ]);
        }
        
        WeighbridgeEntry::whereIn('id', $serviceEntryIds)
            ->update(['is_service_charge_paid' => 1]); 
            $details = [
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => 11,
                    'sub_ledger_id' => $paymentMethod == 1 ? $request->input('bank_sub_ledger_id'): 103,
                    'debit_amount' => $totalServiceCharge,
                    'credit_amount' => null,
                    'description' => '',
                ],
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => 10,
                    'sub_ledger_id' => 101,
                    'debit_amount' => null,
                    'credit_amount' => $totalServiceCharge,
                    'description' => '',
                ],
            ];
    
            JournalDetail::insert($details);
    }

    // Update repayments status
    $totalRepaymentAmount = 0;
    if (!empty($repaymentIds)) {

        $journal = JournalEntry::create([
            'journal_date' => Carbon::now()->toDateString(), // YYYY-MM-DD
            'description' => 'Receipt Loan Repayment Receipt#'.$receipt->id,
        ]);

        foreach($repaymentIds as $repaymentId) {

            $repaymentAmount = OwnerLoanRepayment::find($repaymentId)?->amount;
            $totalRepaymentAmount += $repaymentAmount;
            
            ReceiptDetail::create([
                'receipt_id' => $receipt->id,
                'entry_type' => 'loan',
                'entry_id'   => $repaymentId,
                'amount'     => $repaymentAmount,
            ]);

        }

        $details = [
            [
                'journal_id' => $journal->id,
                'ledger_id' => 11,
                'sub_ledger_id' =>  $paymentMethod == 1 ? $request->input('bank_sub_ledger_id'): 103,
                'debit_amount' => $totalRepaymentAmount,
                'credit_amount' => null,
                'description' => '',
            ],
            [
                'journal_id' => $journal->id,
                'ledger_id' => 10,
                'sub_ledger_id' => 100,
                'debit_amount' => null,
                'credit_amount' => $totalRepaymentAmount,
                'description' => '',
            ],
        ];

        JournalDetail::insert($details);

        OwnerLoanRepayment::whereIn('id', $repaymentIds)
        ->update(['status' => 'paid']);
    }

    if (!empty($otherIncomeIds)) {

        $journal = JournalEntry::create([
            'journal_date' => Carbon::now()->toDateString(), // YYYY-MM-DD
            'description' => 'Receipt Other Income Receipt#'.$receipt->id,
        ]);

        foreach($otherIncomeIds as $otherIncomeId) {

            $otherIncomeAmount = OtherIncome::find($otherIncomeId)?->amount;
            $categoryId = OtherIncome::find($otherIncomeId)?->income_category_id;

            // Categories: 162, 163, 165 → ledger_id = 10, else ledger_id = 27
            switch ($categoryId) {
                case 162:
                    $ledgerId = 10;
                    $subLedgerId = 97;
                    break;
                case 163:
                    $ledgerId = 10;
                    $subLedgerId = 98;
                    break;
                case 165:
                    $ledgerId = 10;
                    $subLedgerId = 99;
                    break;
                default:
                    $ledgerId = 10;
                    $subLedgerId = null;
                    break;
            }

            $details = [
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => 11,
                    'sub_ledger_id' => $paymentMethod == 1 ? $request->input('bank_sub_ledger_id'): 103,
                    'debit_amount' => $otherIncomeAmount,
                    'credit_amount' => null,
                    'description' => '',
                ],
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => $ledgerId,
                    'sub_ledger_id' => $subLedgerId,
                    'debit_amount' => null,
                    'credit_amount' => $otherIncomeAmount,
                    'description' => '',
                ],
            ];
    
            JournalDetail::insert($details);

            ReceiptDetail::create([
                'receipt_id' => $receipt->id,
                'entry_type' => 'other_income',
                'entry_id'   => $otherIncomeId,
                'amount'     => $otherIncomeAmount,
            ]);

        }

        OtherIncome::whereIn('id', $otherIncomeIds)->update(['status' => 'paid']);
    }

    return redirect()->route('receipts.create')->with('success', 'Payment Receipt created successfully.');
}

    public function show($id)
    {
        $receiptDetails = Receipt::with([
            'details'
        ])->findOrFail($id);

        return view('receipts.show', compact('receiptDetails'));
    }

    public function printReceipt(Receipt $receipt)
    {
        $receipt->load(['details', 'buyer']);

        $pdf = Pdf::loadView('receipts.print', [
            'receipt' => $receipt,
            'from_pdf' => true,
        ])
        ->setPaper('A6', 'portrait')
        ->setOptions([
            'defaultFont' => 'Times-Roman',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true,
            'isFontSubsettingEnabled' => true
        ]);
    
        return $pdf->stream("receipt{$receipt->id}.pdf");
    }
}
