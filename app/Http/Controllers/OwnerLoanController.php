<?php

namespace App\Http\Controllers;

use App\Models\JournalDetail;
use App\Models\JournalEntry;
use App\Models\Membership;
use App\Models\Owner;
use App\Models\OwnerLoan;
use App\Models\Side;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OwnerLoanController extends Controller
{
    public function index()
    {
        $ownerLoans = OwnerLoan::with('membership')->get();
        return view('owner_loans_admin.index', compact('ownerLoans'));
    }

    public function myLoans(Request $request) {
        // Get the logged-in owner
      $ownerId =auth('owner')->id(); 
      $salterns = Membership::where('owner_id', $ownerId)->get();
      $owner = Owner::findOrFail($ownerId);

      
    
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
        $ownerId = auth('owner')->id(); 
        $memberships = Membership::where('owner_id', $ownerId)->get();
        return view('owner_loans.create', compact('memberships'));
    }

    public function adminCreateOwnerLoan()
    {
        $sides  = Side::all();
        return view('owner_loans_admin.create', compact('sides'));
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

    public function show(OwnerLoan $ownerLoan)
    {
        $ownerLoan->load(['ownerLoanRepayment']); // load buyer relationship if needed
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
        $loan->approved_by       =  auth('web')->id();
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
            ->where('is_active', 1)
            ->with('owner')  // Eager load the owner
            ->first();
        
            if ($membership) {
                $loans = OwnerLoan::where('membership_id', $membership->id)
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
        
        $html = view('weighbridge_entries.loan-details', [
            'saltern' => $saltern,
            'membership' => '',
            'loans' => $loans,
        ])->render();
    
        return response()->json($html);
    }

    public function getLoanDetails($saltern_id)
    {
        $saltern = [];
        $loans = [];

        $membership = Membership::where('saltern_id', $saltern_id)
            ->where('is_active', 1)
            ->with('owner')  // Eager load the owner
            ->first();
        
            if ($membership) {
                $loans = OwnerLoan::where('membership_id', $membership->id)
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
        
        $html = view('owner_loans_admin.loan-details', [
            'saltern' => $saltern,
            'membership' => '',
            'loans' => $loans,
        ])->render();
    
        return response()->json($html);
    }

    public function adminStoreOwnerLoan(Request $request){
        $validated = $request->validate([
            'membership_id' => 'required|exists:memberships,id',
            'loan_amount' => 'required|numeric|min:1',
            'loan_type' => 'required|in:old,new',
        ]);

        OwnerLoan::create([
            'membership_id' => $validated['membership_id'],
            'requested_amount'  => $validated['loan_amount'],
            'approved_amount'  => $validated['loan_type']  === 'old' ? $validated['loan_amount'] : null,
            'purpose'       => $validated['purpose'] ?? null,
            'status'        => $validated['loan_type'] === 'old' ? 'approved' : 'pending',
            'is_migrated'   => $validated['loan_type'] === 'old',
            'created_by'    => auth('web')->id(),
        ]);

        if ($validated['loan_type']  === 'old') {
            $journal = JournalEntry::create([
                'journal_date' => Carbon::now()->toDateString(), // YYYY-MM-DD
                'description' => 'Owner Loan from old system',
            ]);
    
            $details = [
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => 12,  
                    'sub_ledger_id' => 115,
                    'debit_amount' => $validated['loan_amount'],
                    'credit_amount' => null,
                    'description' => 'Owner Loan',
                ],
            ];
    
            // 3. Bulk insert details
            JournalDetail::insert($details);
    
        }

        return redirect()->route('admin.owner_loans.create')->with('success', 'Owner Loan request submitted successfully.');
    }
}
