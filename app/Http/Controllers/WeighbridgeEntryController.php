<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
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
        return view('weighbridge_entries.create', compact('owners', 'buyers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|string',
            'initial_weight' => 'required|numeric',
            'tare_weight' => 'required|numeric',
            'transaction_date' => 'required|date',
            'owner_id' => 'required|exists:owners,id',
            'buyer_id' => 'required|exists:buyers,id',
        ]);

        WeighbridgeEntry::create($request->all());

        return redirect()->route('weighbridge_entries.index')->with('success', 'Weighbridge entry created successfully.');
    }
}
