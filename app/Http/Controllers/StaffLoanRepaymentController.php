<?php

namespace App\Http\Controllers;

use App\Models\JournalDetail;
use App\Models\JournalEntry;
use App\Models\StaffLoan;
use App\Models\StaffLoanRepayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $validated = $request->validate([
            'staff_loan_id' => 'required|exists:staff_loans,id',
            'amount' => 'required|numeric|min:0.01',
            'repayment_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {

            // Lock the loan row while processing
            $loan = StaffLoan::with(['user', 'staffLoanRepayment'])
                ->lockForUpdate()
                ->findOrFail($validated['staff_loan_id']);

            $paidAmount = $loan->staffLoanRepayment()->sum('amount');

            $outstandingBalance = $loan->approved_amount - $paidAmount;

            if ($validated['amount'] > $outstandingBalance) {

                DB::rollBack();

                return back()
                    ->withInput()
                    ->withErrors([
                        'amount' => 'Repayment amount exceeds the outstanding balance.'
                    ]);
            }

            $repayment = StaffLoanRepayment::create([
                'staff_loan_id'   => $loan->id,
                'amount'          => $validated['amount'],
                'repayment_date'  => $validated['repayment_date'] ?? now()->toDateString(),
                'payment_method'  => 'Cash',
                'notes'           => $validated['notes'],
                'status'          => 'paid',
            ]);

            $journal = JournalEntry::create([
                'journal_date' => now()->toDateString(),
                'description'  => 'Staff Loan Cash Repayment - Loan #' . $loan->id,
            ]);

            JournalDetail::insert([
                [
                    'journal_id'     => $journal->id,
                    'ledger_id'      => 11,
                    'sub_ledger_id'  => 103,
                    'debit_amount'   => $validated['amount'],
                    'credit_amount'  => null,
                    'description'    => 'Cash received',
                ],
                [
                    'journal_id'     => $journal->id,
                    'ledger_id'      => 12,
                    'sub_ledger_id'  => 116,
                    'debit_amount'   => null,
                    'credit_amount'  => $validated['amount'],
                    'description'    => 'Staff loan repayment',
                ],
            ]);

            DB::commit();

            /*
         |-------------------------------------------------------
         | Send SMS AFTER commit
         |-------------------------------------------------------
         */

            // SmsService::sendLoanRepaymentReceipt($loan, $repayment);

            return redirect()
                ->route('staff-loan-repayments.create-for-loan', $loan->id)
                ->with('success', 'Repayment recorded successfully.');
        } catch (\Throwable $e) {

            DB::rollBack();

            //Log::error($e);

            return back()
                ->withInput()
                ->with('error', 'Unable to process repayment.');
        }
    }

    public function printStaffReceipt(StaffLoanRepayment $repayment)
    {
        $repayment->load([
            'staffLoan',
        ]);
        $membershipId = $repayment->staffLoan->user_id;
        $outstandingAmount = 0;

        $pdf = Pdf::loadView('staff_loan_repayments.print_receipt', [
            'repayment' => $repayment,
            'outstandingAmount' => $outstandingAmount,
            'from_pdf' => true
        ])->setPaper('A6', 'portrait')
            ->setOptions([
                'defaultFont' => 'Times-Roman',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isFontSubsettingEnabled' => true,
            ]);

        return $pdf->stream("loan_repayment_receipt_{$repayment->id}.pdf");
    }
}
