<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\StaffLoan;
use Illuminate\Http\Request;

class StaffLoanRequestController extends Controller
{
    public function index()
    {
        $userId = auth('web')->id(); 
       
        $staffLoans = StaffLoan::where('user_id', $userId)->get();
        return view('staff_loans.index', compact('staffLoans'));
    }

    public function create()
    {
        return view('staff_loans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'requested_amount' => 'required|numeric|min:1',
            'purpose' => 'nullable|string|max:255',
        ]);
        $userId = auth('web')->id();

        $validated['user_id'] = $userId;

        StaffLoan::create($validated);

        return redirect()->route('staff-loans.create')->with('success', 'Loan request submitted successfully.');
    }

    public function showMyLoan($id)
    {
        $ownerLoan = StaffLoan::with(['staffLoanRepayment'])->findOrFail($id);

        return view('staff_loans.show', compact('ownerLoan'));
    }
}
