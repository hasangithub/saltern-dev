<?php

namespace App\Http\Controllers;

use App\Models\OwnerLoan;
use App\Models\OwnerLoanRepayment;
use Illuminate\Http\Request;

class OwnerLoanRepaymentController extends Controller
{
    public function createForLoan($loanId)
    {
        $ownerLoan = OwnerLoan::with(['membership', 'ownerLoanRepayment'])->findOrFail($loanId);
        $outstandingBalance = $ownerLoan->approved_amount - $ownerLoan->ownerLoanRepayment->sum('amount');

        return view('owner_loan_repayments.create', compact('ownerLoan', 'outstandingBalance'));
    }

    public function storeForCash(Request $request)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'owner_loan_id' => 'required|exists:owner_loans,id',
            'amount' => 'required|numeric|min:0.01',
            'repayment_date' => 'required|date',
            'payment_method' => 'required|string|in:Cash,Bank Transfer',
            'notes' => 'nullable|string',
        ]);

        // Retrieve the loan request
        $loan = OwnerLoan::findOrFail($validated['owner_loan_id']);

        // Calculate the outstanding balance
        $outstandingBalance = $loan->approved_amount - $loan->ownerLoanRepayment->sum('amount');
        
        // Check if repayment amount exceeds the outstanding balance
        if ($validated['amount'] > $outstandingBalance) {
            return back()->withErrors(['amount' => 'Repayment amount exceeds the outstanding balance.']);
        }

        // Create the repayment record
        $repayment = OwnerLoanRepayment::create([
            'owner_loan_id' => $validated['owner_loan_id'],
            'amount' => $validated['amount'],
            'repayment_date' => $validated['repayment_date'],
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'],
        ]);

        // Optionally update loan status if fully paid
        if ($outstandingBalance - $validated['amount'] == 0) {
            $loan->status = 'Paid';
            $loan->save();
        }

        // Return a response to show success
        return redirect()->route('loan-repayments.create-for-loan', $loan->id)
                         ->with('success', 'Repayment recorded successfully!');
    }
}
