<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\StaffLoan;
use App\Models\User;
use Carbon\Carbon;
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

        return view('staff_loans_admin.show', compact('ownerLoan'));
    }

    public function approve(Request $request, $id)
    {
        $loan = StaffLoan::findOrFail($id);

        // Validate and save the approved amount
        $request->validate([
            'approved_amount' => [
                'required',
                'numeric',
                'min:1',
                'max:' . $loan->requested_amount, // ✅ cannot exceed requested amount
            ],
        ]);

        $loan->approved_amount   = $request->approved_amount;
        $loan->approval_comments = $request->approval_comments;
        $loan->approval_date     = date("Y-m-d H:i:s");
        $loan->status = 'approved'; // Update status to approved
        $loan->save();

        return redirect()->route('admin.staff-loans.show', $loan->id)
            ->with('success', 'Loan request approved successfully.');
    }

    public function adminCreateStaffLoan()
    {   
        $users  = User::all();
        return view('staff_loans_admin.create', compact('users'));
    }

    public function getStaffLoanDetails($id)
    {
        $saltern = [];
        $loans = [];

        $user = User::where('id', $id)
            ->first();
        
            if ($user) {
                $loans = StaffLoan::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->where(function ($query) {
                        $query->whereNotNull('voucher_id')
                            ->orWhere(function ($sub) {
                                $sub->whereNull('voucher_id')
                                    ->where('is_migrated', true);
                            });
                    })
                    ->get();
            }
        
        $html = view('staff_loans_admin.loan-details', [
            'saltern' => $saltern,
            'membership' => '',
            'loans' => $loans,
        ])->render();
    
        return response()->json($html);
    }

    public function adminStoreStaffLoan(Request $request){

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'loan_amount' => 'required|numeric|min:1',
            'loan_type' => 'required|in:old,new',
            'purpose' => 'nullable',
        ]);

        $ownerLoan = StaffLoan::create([
            'user_id' => $validated['user_id'],
            'requested_amount'  => $validated['loan_amount'],
            'approved_amount'  => $validated['loan_type']  === 'old' ? $validated['loan_amount'] : null,
            'purpose'       => $validated['purpose'] ?? null,
            'status'        => $validated['loan_type'] === 'old' ? 'approved' : 'pending',
            'is_migrated'   => $validated['loan_type'] === 'old',
            'created_by'    => auth('web')->id(),
        ]);

        return redirect()->route('admin.staff_loans.create')->with('success', 'Staff Loan request submitted successfully.');
    }
}
