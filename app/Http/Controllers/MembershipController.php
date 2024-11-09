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
}