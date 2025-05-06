<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use Illuminate\Http\Request;
use App\Enums\CivilStatus;
use App\Enums\Gender;
use App\Http\Requests\StoreOwnerRequest;
use App\Http\Requests\UpdateOwnerRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class OwnerController extends Controller
{
    public function index()
    {
        $owners = Owner::all(); // Retrieve all owners
        return view('owners.index', compact('owners')); // Pass owners to view
    }

    public function create()
    {
        return view('owners.create', [
            'genders' => Gender::cases(),
            'civilStatuses' => CivilStatus::cases(),
        ]); 
    }

    public function store(StoreOwnerRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $owner = Owner::create($data);

        if ($owner) {
            if ($request->hasFile('profile_picture')) {
                $ownerProfilePath = $request->file('profile_picture')->store('profile', 'public');
                $owner->update(['profile_picture' => $ownerProfilePath]);
            }
        }
        
        return redirect()->route('owners.index')->with('success', 'Owner created successfully.');
    }

    public function show($id)
    {
        $membership = Owner::findOrFail($id);
        
        return view('owners.show', compact('membership'));
    }

        /**
     * Show the form for editing the specified resource.
     */
    public function edit(Owner $owner)
    {
        return view('owners.edit', ['owner'=>$owner]);
    }

    public function update(UpdateOwnerRequest $request, Owner $owner)
    {
        // Validate the incoming request using the StoreOwnerRequest
        $validatedData = $request->validated();

        if ($request->hasFile('profile_picture')) {
    
            if (Storage::disk('public')->exists($owner->profile_picture)) {
                Storage::disk('public')->delete($owner->profile_picture);
            }
    
            // Store the new profile picture
            $validatedData['profile_picture'] = $request->file('profile_picture')->store('profile', 'public');
        }

        // Update the owner details
        $owner->update($validatedData);

        // Redirect back with a success message
        return redirect()->route('owners.edit', $owner->id)->with('success', 'Owner updated successfully');
    }
}