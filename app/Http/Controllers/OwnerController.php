<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
        public function index()
    {
        $owners = Owner::all(); // Retrieve all owners
        return view('owners.index', compact('owners')); // Pass owners to view
    }

    public function create()
    {
        return view('owners.create'); // Return the create owner form view
    }

    public function store(Request $request)
    {
        // Validate form inputs
        $request->validate([
            'full_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'nic' => 'required|string|max:20|unique:owners,nic',
            'address' => 'required|string|max:255',
            'mobile_no' => 'required|string|max:15',
        ]);

        // Create a new owner record in the database
        Owner::create([
            'full_name' => $request->full_name,
            'dob' => $request->dob,
            'nic' => $request->nic,
            'address' => $request->address,
            'mobile_no' => $request->mobile_no,
        ]);

        // Redirect back to the owners list with a success message
        return redirect()->route('owners.index')->with('success', 'Owner created successfully.');
    }
}
