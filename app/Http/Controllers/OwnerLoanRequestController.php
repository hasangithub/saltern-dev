<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\OwnerLoan;
use Illuminate\Http\Request;

class OwnerLoanRequestController extends Controller
{
    public function index()
    {
        $ownerLoans = OwnerLoan::all();
        return view('owner_loans.index', compact('ownerLoans'));
    }

    public function create()
    {
        $memberships = Membership::all();
        return view('owner_loans.create', compact('memberships'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'membership_id' => 'required|exists:memberships,id',
            'requested_amount' => 'required|numeric|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        OwnerLoan::create($validated);

        return redirect()->route('owner-loans.create')->with('success', 'Loan request submitted successfully.');
    }
}
