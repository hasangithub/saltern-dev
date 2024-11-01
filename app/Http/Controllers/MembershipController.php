<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Saltern;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MembershipController extends Controller
{
    public function index()
    {
        $memberships = Membership::all(); // Retrieve all owners
        return view('memberships.index', compact('memberships')); // Pass memberships to view
    }

    // Show the form to create a new membership
    public function create()
    {
        $salterns = Saltern::all();
        $owners = Owner::all();
        return view('memberships.create', compact('salterns', 'owners'));
    }

    // Store a newly created membership in storage
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'saltern_id' => 'required|exists:salterns,id',
            'owner_id' => 'required|exists:owners,id',
             'membership_date' => 'required|date',
             'owner_signature' => 'required|image|max:2048',
             'representative_name' => 'required|string|max:255',
             'representative_signature' => 'required|image|max:2048',
             'is_active' => 'boolean',
        ]);
    
        $validatedData['owner_signature'] = $request->file('owner_signature')->store('signatures', 'public');
        $validatedData['representative_signature'] = $request->file('representative_signature')->store('signatures', 'public');
    
        \Log::info('Validated Data:', $validatedData);

        $membership = Membership::create($validatedData);

        if ($membership) {
            \Log::info('Membership created successfully.');
        } else {
            \Log::error('Failed to create membership.');
        }
        return redirect()->route('memberships.create')->with('success', 'Membership added successfully.');
    }

    public function show($id)
    {
        $membership = Membership::with('owner', 'saltern')->findOrFail($id);
        
        return view('memberships.show', compact('membership'));
    }
}