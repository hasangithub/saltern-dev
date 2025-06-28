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
use App\Models\Receipt;
use App\Models\ReceiptDetail;
use Carbon\Carbon;

class ReceiptController extends Controller
{

    public function index()
    {
        $receipts = Receipt::with(['buyer', 'createdBy', 'details'])->latest()->paginate(20);
        return view('receipts.index', compact('receipts'));
    }

public function create(Request $request)
{
    $buyers = Buyer::all();
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

    return view('receipts.create', compact('buyers', 'pendingServiceCharges', 'pendingLoanRepayments', 'pendingOtherIncomes', 'buyerName', 'buyerId'));
}

public function store(Request $request)
{   
    $request->validate([
        'buyer_id' => 'required|exists:buyers,id',
        'service_entry_ids' => 'array',
        'repayment_ids' => 'array',
        'otherincome_ids' => 'array',
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
        'receipt_date' => now(),
        'total_amount' => $totalAmount,
        'created_by'   => auth('web')->id(),
    ]);

    // Update service entries status
    if (!empty($serviceEntryIds)) {
        foreach($serviceEntryIds as $serviceEntryId) {
            $journal = JournalEntry::create([
                'journal_date' => Carbon::now()->toDateString(), // YYYY-MM-DD
                'description' => 'Receipt Service Charge',
            ]);
    
            $serviceCharge = WeighbridgeEntry::find($serviceEntryId)?->total_amount;

            $details = [
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => 11,
                    'sub_ledger_id' => null,
                    'debit_amount' => $serviceCharge,
                    'credit_amount' => null,
                    'description' => '',
                ],
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => 10,
                    'sub_ledger_id' => null,
                    'debit_amount' => null,
                    'credit_amount' => $serviceCharge,
                    'description' => '',
                ],
            ];
    
            JournalDetail::insert($details);

            ReceiptDetail::create([
                'receipt_id' => $receipt->id,
                'entry_type' => 'weighbridge',
                'entry_id'   => $serviceEntryId,
                'amount'     => $serviceCharge,
            ]);
        }
        WeighbridgeEntry::whereIn('id', $serviceEntryIds)
            ->update(['is_service_charge_paid' => 1]); // Assuming 1 means paid
    }

    // Update repayments status
    if (!empty($repaymentIds)) {
        OwnerLoanRepayment::whereIn('id', $repaymentIds)
            ->update(['status' => 'paid']);

        foreach($repaymentIds as $repaymentId) {

            $repaymentAmount = OwnerLoanRepayment::find($repaymentId)?->amount;

            $journal = JournalEntry::create([
                'journal_date' => Carbon::now()->toDateString(), // YYYY-MM-DD
                'description' => 'Receipt Loan Repayment',
            ]);

            $details = [
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => 11,
                    'sub_ledger_id' => null,
                    'debit_amount' => $repaymentAmount,
                    'credit_amount' => null,
                    'description' => '',
                ],
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => 10,
                    'sub_ledger_id' => null,
                    'debit_amount' => null,
                    'credit_amount' => $repaymentAmount,
                    'description' => '',
                ],
            ];
    
            JournalDetail::insert($details);

            ReceiptDetail::create([
                'receipt_id' => $receipt->id,
                'entry_type' => 'loan',
                'entry_id'   => $repaymentId,
                'amount'     => $repaymentAmount,
            ]);

        }
    }

    if (!empty($otherIncomeIds)) {
        OtherIncome::whereIn('id', $otherIncomeIds)
            ->update(['status' => 'paid']);

        foreach($otherIncomeIds as $otherIncomeId) {

            $otherIncomeAmount = OtherIncome::find($otherIncomeId)?->amount;

            $journal = JournalEntry::create([
                'journal_date' => Carbon::now()->toDateString(), // YYYY-MM-DD
                'description' => 'Receipt Other Income',
            ]);

            $details = [
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => 11,
                    'sub_ledger_id' => null,
                    'debit_amount' => $otherIncomeAmount,
                    'credit_amount' => null,
                    'description' => '',
                ],
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => 10,
                    'sub_ledger_id' => null,
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
    }

    return redirect()->route('receipts.index')->with('success', 'Payment processed successfully.');
}


}
