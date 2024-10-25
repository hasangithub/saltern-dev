<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Saltern;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MembershipController extends Controller
{
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
        // Validate the request
        $request->validate([
            'saltern_id' => 'required|exists:salterns,id',
            'owner_id' => 'required|exists:owners,id',
            'membership_date' => 'required|date',
            'owner_signature' => 'required|image|max:2048', // Maximum size of 2MB
            'representative_name' => 'required|string|max:255',
            'representative_signature' => 'required|image|max:2048', // Maximum size of 2MB
            'additional_terms' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Handle file uploads
        $ownerSignaturePath = $request->file('owner_signature')->store('signatures');
        $representativeSignaturePath = $request->file('representative_signature')->store('signatures');

        // Create the membership record
        Membership::create([
            'saltern_id' => $request->saltern_id,
            'owner_id' => $request->owner_id,
            'membership_date' => $request->membership_date,
            'owner_signature' => $ownerSignaturePath, // Store the path
            'representative_name' => $request->representative_name,
            'representative_signature' => $representativeSignaturePath, // Store the path
            'additional_terms' => $request->additional_terms,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('memberships.create')->with('success', 'Membership added successfully.');
    }

    // Retrieve a signature file
    public function getSignature($filename)
    {
        $path = storage_path('app/signatures/' . $filename);

        // Check if the file exists
        if (!file_exists($path)) {
            abort(404); // Not found
        }

        return response()->file($path);
    }
}
