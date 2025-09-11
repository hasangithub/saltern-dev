<?php

namespace App\Http\Controllers;

use App\Models\JournalDetail;
use App\Models\JournalEntry;
use App\Models\StaffLoan;
use App\Models\StaffLoanRepayment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StaffLoanRepaymentController extends Controller
{
    public function index()
    {
        $repayments = StaffLoanRepayment::with(['staffLoan']) 
        ->orderBy('created_at', 'desc')
        ->get();
        return view('staff_loan_repayments.index', compact('repayments'));
    }

    public function createForLoan($loanId)
    {  
        $ownerLoan = StaffLoan::with(['staffLoanRepayment'])->findOrFail($loanId);
        $outstandingBalance = $ownerLoan->approved_amount - $ownerLoan->staffLoanRepayment->sum('amount');
        $outstandingBalance = number_format($outstandingBalance, 2);
  
        return view('staff_loan_repayments.create', compact('ownerLoan', 'outstandingBalance'));
    }

    public function storeForCash(Request $request)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'staff_loan_id' => 'required|exists:staff_loans,id',
            'amount' => 'required|numeric|min:0.01',
            'repayment_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Retrieve the loan request
        $loan = StaffLoan::with('user')->findOrFail($validated['staff_loan_id']);

        // Calculate the outstanding balance
        $outstandingBalance = $loan->approved_amount - $loan->staffLoanRepayment->sum('amount');

        // Check if repayment amount exceeds the outstanding balance
        if ($validated['amount'] > $outstandingBalance) {
            return back()->withErrors(['amount' => 'Repayment amount exceeds the outstanding balance.']);
        }

        StaffLoanRepayment::create([
            'staff_loan_id' => $validated['staff_loan_id'],
            'amount' => $validated['amount'],
            'repayment_date' => $validated['repayment_date'] ?? date("Y-m-d"),
            'payment_method' => 'Cash',
            'notes' => $validated['notes'],
            'status' => 'paid',
        ]);

        $journal = JournalEntry::create([
            'journal_date' => Carbon::now()->toDateString(), // YYYY-MM-DD
            'description' => 'Loan deducted',
        ]);

        $details = [
            [
                'journal_id' => $journal->id,
                'ledger_id' => 10,
                'sub_ledger_id' => 100,
                'debit_amount' => $validated['amount'],  // cash book debit
                'credit_amount' => null,
                'description' => '',
            ],
            [
                'journal_id' => $journal->id,
                'ledger_id' => 12,
                'sub_ledger_id' => 115,
                'debit_amount' => null,
                'credit_amount' => $validated['amount'], // recevele staff loan
                'description' => '',
            ],
        ];

        JournalDetail::insert($details);

        // Return a response to show success
        return redirect()->route('staff-loan-repayments.create-for-loan', $loan->id)
            ->with('success', 'Repayment recorded successfully!');
    }
}
