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
use Carbon\Carbon;

class ReceiptController extends Controller
{

public function index(Request $request)
{
    $buyers = Buyer::all();
    $pendingServiceCharges = collect();
    $pendingLoanRepayments = collect();
    $pendingOtherIncomes = collect();
    $buyerName = '';

    if ($request->filled('buyer_id')) {
        $buyer = Buyer::find($request->buyer_id);
        if ($buyer) {
            $buyerName = $buyer->business_name;
            
            // Get pending weighbridge service charges
            $pendingServiceCharges = WeighbridgeEntry::where('buyer_id', $buyer->id)->where('is_service_charge_paid', 0)
                ->get();

            // Get pending owner loans assigned to this buyer
            $pendingLoanRepayments = OwnerLoanRepayment::where('buyer_id', $buyer->id)->where('status', 'pending')->get();

            $pendingOtherIncomes = OtherIncome::where('buyer_id', $buyer->id)->where('status', 'pending')->get();
        }
    }

    return view('receipts.index', compact('buyers', 'pendingServiceCharges', 'pendingLoanRepayments', 'pendingOtherIncomes', 'buyerName'));
}

public function store(Request $request)
{   
    $buyerId = $request->input('buyer_id');
    $serviceEntryIds = $request->input('service_entry_ids', []);
    $repaymentIds = $request->input('repayment_ids', []);
    $otherIncomeIds = $request->input('otherincome_ids', []);

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

        }
    }

    return redirect()->route('receipts.index')->with('success', 'Payment processed successfully.');
}


}
