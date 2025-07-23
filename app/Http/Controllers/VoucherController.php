<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\JournalDetail;
use App\Models\JournalEntry;
use App\Models\Ledger;
use App\Models\OwnerLoan;
use App\Models\PaymentMethod;
use App\Models\SubAccountGroup;
use App\Models\SubLedger;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $memberships = Voucher::with(['paymentMethod', 'bank'])->get();

        return view('vouchers.index', compact('memberships'));
    }

    public function create()
    {
        $paymentMethods = PaymentMethod::all();
        $banks = SubLedger::where('ledger_id', 11)->get();
        $ledgers = Ledger::all();
        $subAccounts = SubAccountGroup::all();
        $ownerLoans = OwnerLoan::with('membership')->where('status', 'approved')->whereNull('voucher_id')->where('is_migrated', false)->get();

        return view('vouchers.create', compact('paymentMethods', 'banks', 'ledgers', 'ownerLoans', 'subAccounts'));
    }

    public function store(Request $request)
    { 
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'description' => 'required|string',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'bank_sub_ledger_id' => 'nullable|exists:sub_ledgers,id',
            'cheque_no' => 'nullable|string|max:50',
            'cheque_date' => 'nullable|date',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'status' => 'nullable',
            'owner_loan_id' => 'nullable|exists:owner_loans,id',
            'ledger_id' => 'required_without:owner_loan_id',
            'sub_ledger_id' => 'nullable|exists:sub_ledgers,id',
        ]);

        $voucher = Voucher::create($validated);

        if ($request->filled('owner_loan_id')) {
            $ownerLoan = OwnerLoan::findOrFail($request->owner_loan_id);
        
            $ownerLoan->update([
                'voucher_id' => $voucher->id,
            ]);
        }

        
        $journal = JournalEntry::create([
            'journal_date' => Carbon::now()->toDateString(), // YYYY-MM-DD
            'description' => "Voucher#".$voucher->id." ".$validated['description'],
        ]);

        if ($request->filled('owner_loan_id')) {

            $details = [
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => 12,
                    'sub_ledger_id' => 115,
                    'debit_amount' => $validated['amount'],
                    'credit_amount' => null,
                    'description' => "Voucher#".$voucher->id." Loan Disbursed to Owner#". $ownerLoan->membership->owner_id,
                ],
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => 11,
                    'sub_ledger_id' => $validated['payment_method_id'] == 1 ? $validated['bank_sub_ledger_id'] : 103,
                    'debit_amount' => null,
                    'credit_amount' => $validated['amount'],
                    'description' => "Voucher#".$voucher->id,
                ],
            ];
    

        } else {
            $details = [
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => $request->input('ledger_id'),
                    'sub_ledger_id' => $request->input('sub_ledger_id'),
                    'debit_amount' => $validated['amount'],
                    'credit_amount' => null,
                    'description' => "Voucher#".$voucher->id." ".$validated['description'],
                ],
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => 11,
                    'sub_ledger_id' => $validated['payment_method_id'] == 1 ? $validated['bank_sub_ledger_id'] : 103,
                    'debit_amount' => null,
                    'credit_amount' => $validated['amount'],
                    'description' => "Voucher#".$voucher->id." ".$validated['description'],
                ],
            ];
    
        }

        JournalDetail::insert($details);

        return redirect()->route('vouchers.create')->with('success', 'Voucher request submitted successfully.');
    }

    public function show($id)
    {
        $voucher = Voucher::findOrFail($id);

        return view('vouchers.show', compact('voucher'));
    }

    public function approve(Request $request, $id)
    {
        $voucher = Voucher::findOrFail($id);

        // Validate and save the approved amount
        $request->validate([
            'note' => 'nullable|string|min:1',
        ]);

        $voucher->note   = $request->note;
        $voucher->status = 'approved'; // Update status to approved
        $voucher->save();

        return redirect()->route('vouchers.show', $voucher->id)
            ->with('success', 'Voucher approved successfully.');
    }

    public function printVoucherReport(Request $request)
{
    $request->validate([
        'from_date' => 'nullable|date',
        'to_date' => 'nullable|date|after_or_equal:from_date',
    ]);

    $fromDate = Carbon::parse($request->from_date)->startOfDay();
    $toDate = Carbon::parse($request->to_date)->endOfDay();

    $query = Voucher::with(['paymentMethod', 'bank']);

    if ($request->filled('from_date') && $request->filled('to_date')) {
        $query->whereBetween('created_at', [
            $fromDate,
            $toDate
        ]);
    }

    $vouchers = $query->orderBy('created_at', 'desc')->get();

    $totalAmount = $vouchers->sum('amount');

    $pdf = Pdf::loadView('reports.voucher.report', [
        'vouchers' => $vouchers,
        'totalAmount' => $totalAmount,
        'fromDate' => $request->from_date,
        'toDate' => $request->to_date,
    ])
    ->setPaper('A4', 'portrait')
    ->setOptions([
        'defaultFont' => 'DejaVu Sans',
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
    ]);

    return $pdf->stream('voucher-report.pdf');
}

public function printVoucher($id)
{
    $voucher = Voucher::findOrFail($id);

    $pdf = Pdf::loadView('vouchers.print', compact('voucher'))->setPaper('a4', 'portrait');

    return $pdf->stream("voucher_{$voucher->id}.pdf");
}


}
