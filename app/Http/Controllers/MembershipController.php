<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\Saltern;
use App\Models\Owner;
use App\Models\Representative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Enums\CivilStatus;
use App\Enums\Gender;
use App\Http\Requests\StoreMembershipRequest;
use App\Http\Requests\StoreRepresentativeRequest;
use App\Http\Requests\UpdateMembershipRequest;


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
        return view('memberships.create', [
            'genders' => Gender::cases(),
            'civilStatuses' => CivilStatus::cases(),
        ], compact('salterns', 'owners'));
    }

    // Store a newly created membership in storage
    public function store(StoreMembershipRequest $membershipRequest, StoreRepresentativeRequest $representativeRequest)
    {
       
         // Store Membership Details
         $membership = Membership::create($membershipRequest->validated());

        // Add the membership_id to the representative data
        $representativeData = $representativeRequest->validated();
        if ($representativeRequest->hasFile('profile_picture')) {
            $ownerSignaturePath = $representativeRequest->file('profile_picture')->store('profile', 'public');
            $representativeData['profile_picture'] = $ownerSignaturePath;
        }

        $representativeData['membership_id'] = $membership->id;

        // Store the representative, linking it to the membership
        $representative = Representative::create($representativeData);

        if ($membership && $representative) {
            if ($membershipRequest->hasFile('owner_signature')) {
                $ownerSignaturePath = $membershipRequest->file('owner_signature')->store('signatures', 'public');
                $membership->update(['owner_signature' => $ownerSignaturePath]);
            }
    
            if ($membershipRequest->hasFile('representative_signature')) {
                $representativeSignaturePath = $membershipRequest->file('representative_signature')->store('signatures', 'public');
                $membership->update(['representative_signature' => $representativeSignaturePath]);
            }
        }

       // $validatedData['owner_signature'] = $request->file('owner_signature')->store('signatures', 'public');
       // $validatedData['representative_signature'] = $request->file('representative_signature')->store('signatures', 'public');
    
        return redirect()->route('memberships.create')->with('success', 'Membership added successfully.');
    }

    public function show($id)
    {
        $membership = Membership::with('owner', 'saltern')->findOrFail($id);
        
        return view('memberships.show', compact('membership'));
    }

    public function edit(Membership $membership)
    {
        $salterns = Saltern::all();
        $owners = Owner::all();
        return view('memberships.edit', ['membership'=>$membership, 'salterns'=>$salterns, 'owners'=>$owners]);
    }

    public function update(UpdateMembershipRequest $request, Membership $membership)
    {   
        // $validatedData = $request->validated();
        // $representativeData = $representativeRequest->validated();

        $membershipData = $request->validated()['membership'];
        $representativeData = $request->validated()['representative'];

        if ($request->hasFile('membership.owner_signature')) {
            $ownerSignaturePath = $request->file('membership.owner_signature')->store('signatures', 'public');
            $membershipData['owner_signature'] = $ownerSignaturePath;
        }

        if ($request->hasFile('membership.representative_signature')) {
            $representativeSignaturePath = $request->file('membership.representative_signature')->store('signatures', 'public');
            $membershipData['representative_signature'] = $representativeSignaturePath;
        }
 
        $membership->update($membershipData);

        if ($request->hasFile('representative.profile_picture')) {

            // Delete the old profile picture if it exists
            if (Storage::disk('public')->exists($membership->representative->profile_picture)) {
                Storage::disk('public')->delete($membership->representative->profile_picture);
            }
    
            // Store the new profile picture
            $representativeData['profile_picture'] = $request->file('representative.profile_picture')->store('profile', 'public');
        }

        $membership->representative()->updateOrCreate(
            ['membership_id' => $membership->id], // Match by membership_id
            $representativeData
        );

        // Redirect back with a success message
        return redirect()->route('memberships.edit', $membership->id)->with('success', 'Membership updated successfully');
    }
}