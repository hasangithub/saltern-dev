<?php

namespace App\Http\Controllers;

use App\Models\StaffLoan;
use Illuminate\Http\Request;

class StaffLoanController extends Controller
{
    public function index()
    {
        $staffLoans = StaffLoan::all();
        return view('staff_loans_admin.index', compact('staffLoans'));
    }

    public function show($id)
    {
        $ownerLoan = StaffLoan::findOrFail($id);

        return view('owner_loans_admin.show', compact('ownerLoan'));
    }

    public function approve(Request $request, $id)
    {
        $loan = StaffLoan::findOrFail($id);

        // Validate and save the approved amount
        $request->validate([
            'approved_amount' => 'required|numeric|min:1',
        ]);

        $loan->approved_amount   = $request->approved_amount;
        $loan->approval_comments = $request->approval_comments;
        $loan->approval_date     = date("Y-m-d H:i:s");
        $loan->status = 'approved'; // Update status to approved
        $loan->save();

        return redirect()->route('admin.staff-loans.show', $loan->id)
            ->with('success', 'Loan request approved successfully.');
    }
}
