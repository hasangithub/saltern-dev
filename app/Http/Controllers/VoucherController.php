<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Ledger;
use App\Models\PaymentMethod;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $memberships = Voucher::all();
        return view('vouchers.index', compact('memberships'));
    }

    public function create()
    {
        $paymentMethods = PaymentMethod::all();
        $banks = Bank::all();
        $ledgers = Ledger::all();
        return view('vouchers.create', compact('paymentMethods', 'banks', 'ledgers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'description' => 'required|string',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'bank_id' => 'nullable|exists:banks,id',
            'cheque_no' => 'nullable|string|max:50',
            'cheque_date' => 'nullable|date',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'status' => 'nullable'
        ]);

        Voucher::create($validated);

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
