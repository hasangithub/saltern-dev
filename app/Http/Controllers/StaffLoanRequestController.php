<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\StaffLoan;
use Illuminate\Http\Request;

class StaffLoanRequestController extends Controller
{
    public function index()
    {
        $ownerLoans = StaffLoan::all();
        return view('staff_loans.index', compact('ownerLoans'));
    }

    public function create()
    {
        $memberships = Membership::all();
        return view('staff_loans.create', compact('memberships'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'membership_id' => 'required|exists:memberships,id',
            'requested_amount' => 'required|numeric|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        OwnerLoan::create($validated);

        return redirect()->route('staff-loans.create')->with('success', 'Loan request submitted successfully.');
    }
}
