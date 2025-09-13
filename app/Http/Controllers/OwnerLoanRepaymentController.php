<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\JournalDetail;
use App\Models\JournalEntry;
use App\Models\OwnerLoan;
use App\Models\OwnerLoanRepayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\SmsService;
use Barryvdh\DomPDF\Facade\Pdf;

class OwnerLoanRepaymentController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function index()
    {
        $repayments = OwnerLoanRepayment::with(['ownerLoan', 'buyer']) // optional relationships
            ->orderBy('created_at', 'desc')
            ->get();
        return view('owner_loan_repayments.index', compact('repayments'));
    }

    public function createForLoan($loanId)
    {
        $ownerLoan = OwnerLoan::with(['membership', 'ownerLoanRepayment'])->findOrFail($loanId);
        $outstandingBalance = $ownerLoan->approved_amount - $ownerLoan->ownerLoanRepayment->sum('amount');
        $outstandingBalance = number_format($outstandingBalance, 2);
        $buyers = Buyer::all();

        return view('owner_loan_repayments.create', compact('ownerLoan', 'outstandingBalance', 'buyers'));
    }

    public function storeForCash(Request $request)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'owner_loan_id' => 'required|exists:owner_loans,id',
            'buyer_id' => 'required|exists:buyers,id',
            'amount' => 'required|numeric|min:0.01',
            'repayment_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Retrieve the loan request
        $loan = OwnerLoan::with('membership')->findOrFail($validated['owner_loan_id']);

        // Calculate the outstanding balance
        $outstandingBalance = $loan->approved_amount - $loan->ownerLoanRepayment->sum('amount');

        // Check if repayment amount exceeds the outstanding balance
        if ($validated['amount'] > $outstandingBalance) {
            return back()->withErrors(['amount' => 'Repayment amount exceeds the outstanding balance.']);
        }

        OwnerLoanRepayment::create([
            'owner_loan_id' => $validated['owner_loan_id'],
            'buyer_id' => $validated['buyer_id'],
            'amount' => $validated['amount'],
            'repayment_date' => $validated['repayment_date'] ?? date("Y-m-d"),
            'payment_method' => 'Cash',
            'notes' => $validated['notes'],
            'status' => 'pending',
        ]);

        $journal = JournalEntry::create([
            'journal_date' => Carbon::now()->toDateString(), // YYYY-MM-DD
            'description' => 'Loan deducted by Buyer#' . $validated['buyer_id'] . "from MembershipId#" . $loan->membership_id,
        ]);

        $details = [
            [
                'journal_id' => $journal->id,
                'ledger_id' => 10,
                'sub_ledger_id' => 100,
                'debit_amount' => $validated['amount'],
                'credit_amount' => null,
                'description' => '',
            ],
            [
                'journal_id' => $journal->id,
                'ledger_id' => 12,
                'sub_ledger_id' => 115,
                'debit_amount' => null,
                'credit_amount' => $validated['amount'],
                'description' => '',
            ],
        ];

        JournalDetail::insert($details);

        $membership = $loan->membership;
        $waikal = $membership->saltern->yahai->name . " " . $membership->saltern->name;
        $ownerPhone = $membership->owner->phone_number;
        $todayDate = date('Y-m-d');
        $totalPaidNow = $validated['amount'];
        $totalOutstanding = $outstandingBalance - $validated['amount'];

        $smsCommon = "{$todayDate}\n"
            . "{$membership->owner->name_with_initial}\n"
            . "{$waikal}";

        if ($totalPaidNow > 0) {
            $smsCommon .= "\nLoan Paid : Rs. " . number_format($totalPaidNow, 2)
                . "\nOutstanding Balance: Rs. " . number_format($totalOutstanding, 2);
        }

        try {
            if (!empty($ownerPhone)) {
                $this->smsService->sendSms($ownerPhone, $smsCommon);
            }
        } catch (\Exception $e) {
        }



        // Return a response to show success
        return redirect()->route('loan-repayments.create-for-loan', $loan->id)
            ->with('success', 'Repayment recorded successfully!');
    }

    public function printReceipt(OwnerLoanRepayment $repayment)
    {
        $repayment->load([
            'ownerLoan.membership.owner',
            'ownerLoan.membership.saltern.yahai',
        ]);
        $membershipId = $repayment->ownerLoan->membership_id;
        $outstandingAmount = $this->getOutstandingLoanAmount($membershipId);

        $pdf = Pdf::loadView('owner_loan_repayments.print_receipt', [
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

    private function getOutstandingLoanAmount($membershipId)
    {
        // Get all loans for the membership
        $loanIds = OwnerLoan::where('membership_id', $membershipId)
            ->pluck('id');

        // Total approved amount
        $totalApproved = OwnerLoan::whereIn('id', $loanIds)
            ->sum('approved_amount');

        // Total repaid amount
        $totalRepaid = OwnerLoanRepayment::whereIn('owner_loan_id', $loanIds)
            ->sum('amount');

        // Outstanding = approved - repaid
        return $totalApproved - $totalRepaid;
    }

    public function destroy($id)
    {
        $repayment = OwnerLoanRepayment::findOrFail($id);
        $repayment->deleted_by = auth('web')->id();
        $repayment->save();
        $repayment->delete(); // soft delete
        return redirect()->back()->with('success', 'Repayment deleted successfully.');
    }
}
