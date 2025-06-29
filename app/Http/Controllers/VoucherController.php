<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\JournalDetail;
use App\Models\JournalEntry;
use App\Models\Ledger;
use App\Models\OwnerLoan;
use App\Models\PaymentMethod;
use App\Models\SubLedger;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');

        $pendingCount  = Voucher::where('status', 'pending')->count();
        $approvedCount = Voucher::where('status', 'approved')->count();
        $rejectedCount = Voucher::where('status', 'rejected')->count();
        $cardOutline = "";

        $entriesQuery = Voucher::with(['paymentMethod']);
        
        if ($status) {

            $entriesQuery->where('status', $status);

            switch ($status) {
                case 'pending':
                    $cardOutline = " card-outline card-warning ";
                    break;
                case 'approved':
                    $cardOutline = " card-outline card-primary ";
                    break;
                case 'rejected':
                    $cardOutline = " card-outline card-danger ";
                    break;
                default:
                    // If no specific status is provided, get all entries
                    break;
            }
        }

        $memberships = $entriesQuery->get();

        return view('vouchers.index', compact('memberships', 'pendingCount', 'approvedCount', 'rejectedCount', 'cardOutline'));
    }

    public function create()
    {
        $paymentMethods = PaymentMethod::all();
        $banks = SubLedger::where('ledger_id', 11)->get();
        $ledgers = Ledger::all();
        $ownerLoans = OwnerLoan::with('membership')->where('status', 'approved')->whereNull('voucher_id')->where('is_migrated', false)->get();

        return view('vouchers.create', compact('paymentMethods', 'banks', 'ledgers', 'ownerLoans'));
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
            'description' => $validated['description'],
        ]);

        if ($request->filled('owner_loan_id')) {

            $details = [
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => 12,
                    'sub_ledger_id' => 115,
                    'debit_amount' => $validated['amount'],
                    'credit_amount' => null,
                    'description' => '',
                ],
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => 11,
                    'sub_ledger_id' => $validated['payment_method_id'] == 1 ? $validated['bank_sub_ledger_id'] : 103,
                    'debit_amount' => null,
                    'credit_amount' => $validated['amount'],
                    'description' => '',
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
                    'description' => $validated['description'],
                ],
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => 11,
                    'sub_ledger_id' => $validated['payment_method_id'] == 1 ? $validated['bank_sub_ledger_id'] : 103,
                    'debit_amount' => null,
                    'credit_amount' => $validated['amount'],
                    'description' => $validated['description'],
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
            'note' => 'required|string|min:1',
        ]);

        $voucher->note   = $request->note;
        $voucher->status = 'approved'; // Update status to approved
        $voucher->save();

        return redirect()->route('vouchers.show', $voucher->id)
            ->with('success', 'Loan request approved successfully.');
    }
}
