<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\Membership;
use App\Models\Owner;
use App\Models\WeighbridgeEntry;
use Illuminate\Http\Request;

class WeighbridgeEntryController extends Controller
{
    public function index()
    {
        // Fetch all weighbridge entries with related owners and buyers
        $entries = WeighbridgeEntry::with(['owner', 'buyer'])->get();
        return view('weighbridge_entries.index', compact('entries'));
    }
    
    public function create()
    {
        $owners = Owner::all(); // Fetch all owners
        $buyers = Buyer::all(); // Fetch all buyers
        $memberships = Membership::all(); 
        return view('weighbridge_entries.create', compact('owners', 'buyers', 'memberships'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|string',
            'initial_weight' => 'required|numeric',
            'transaction_date' => 'required|date',
            'membership_id' => 'required|exists:memberships,id',
            'buyer_id' => 'required|exists:buyers,id',
        ]);

        $membership = Membership::findOrFail($request->membership_id);
        $data = $request->all();
        $data['owner_id'] = $membership->owner_id;

        WeighbridgeEntry::create($data);

        return redirect()->route('weighbridge_entries.index')->with('success', 'Weighbridge entry created successfully.');
    }

    public function show($id)
    {
        $weighbridgeEntry = WeighbridgeEntry::findOrFail($id);

        return view('weighbridge_entries.show', compact('weighbridgeEntry'));
    }

    public function addTare(Request $request, $id)
    {
        $WeighbridgeEntry = WeighbridgeEntry::findOrFail($id);

        // Validate and save the approved amount
        $request->validate([
            'tare_weight' => 'required',
        ]);

        $WeighbridgeEntry->tare_weight   = $request->tare_weight;
        $WeighbridgeEntry->save();

        return redirect()->route('weighbridge_entries.show', $WeighbridgeEntry->id)
            ->with('success', 'Tare weight updated successfully.');
    }

}
