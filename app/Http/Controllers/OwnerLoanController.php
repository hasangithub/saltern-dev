<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Owner;
use App\Models\OwnerLoan;
use Illuminate\Http\Request;

class OwnerLoanController extends Controller
{
    public function index()
    {
        $ownerLoans = OwnerLoan::all();
        return view('owner_loans_admin.index', compact('ownerLoans'));
    }

    public function myLoans(Request $request) {
        // Get the logged-in owner
      //  $owner = Auth::guard('owner')->user();
      $salterns = Membership::where('owner_id', 2)->get();
      $owner = Owner::findOrFail(2);

      
    
      if ($request->has('saltern_id') && !is_null($request->saltern_id) && $request->saltern_id !== '') {
        $loans = OwnerLoan::with('membership')
        ->whereHas('membership', function ($query) use ($request) {
            $query->where('membership_id', $request->saltern_id);
        })
        ->get();
      } else {
        $loans = OwnerLoan::with('membership')
        ->whereHas('membership', function ($query) use ($owner) {
            $query->where('owner_id', $owner->id);
        })
        ->get();
      }

        // Return view with loans
        return view('owner_loans.index', compact('loans', 'salterns'));
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

    public function showMyLoan($id)
    {
        $ownerLoan = OwnerLoan::with(['membership', 'ownerLoanRepayment'])->findOrFail($id);

        return view('owner_loans.show', compact('ownerLoan'));
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
