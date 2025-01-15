<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\OwnerLoan;
use Illuminate\Http\Request;

class OwnerLoanController extends Controller
{
    public function index()
    {
        $ownerLoans = OwnerLoan::all();
        return view('owner_loans_admin.index', compact('ownerLoans'));
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
            'purpose' => 'nullable|string|max:255',
        ]);

        OwnerLoan::create($validated);

        return redirect()->route('owner-loans.create')->with('success', 'Loan request submitted successfully.');
    }

    public function show($id)
    {
        $ownerLoan = OwnerLoan::findOrFail($id);

        return view('owner_loans_admin.show', compact('ownerLoan'));
    }

    public function approve(Request $request, $id)
    {
        $loan = OwnerLoan::findOrFail($id);

        // Validate and save the approved amount
        $request->validate([
            'approved_amount' => 'required|numeric|min:1',
        ]);

        $loan->approved_amount   = $request->approved_amount;
        $loan->approval_comments = $request->approval_comments;
        $loan->approval_date     = date("Y-m-d H:i:s");
        $loan->status = 'approved'; // Update status to approved
        $loan->save();

        return redirect()->route('owner-loans.show', $loan->id)
            ->with('success', 'Loan request approved successfully.');
    }

    public function getSalternDetails($saltern_id)
    {
        $saltern = [];
        $loans = [];

        $membership = Membership::where('saltern_id', $saltern_id)
            ->with('owner')  // Eager load the owner
            ->first();
        
        $loans = OwnerLoan::where('membership_id', $membership->id)->get();

        $html = view('weighbridge_entries.loan-details', [
            'saltern' => $saltern,
            'membership' => '',
            'loans' => $loans,
        ])->render();
    
        return response()->json($html);
    }
}
