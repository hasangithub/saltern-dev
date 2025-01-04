<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\Membership;
use App\Models\Owner;
use App\Models\WeighbridgeEntry;
use Illuminate\Http\Request;

class WeighbridgeEntryController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');

        $pendingCount = WeighbridgeEntry::where('status', 'pending')->count();
        $approvedCount = WeighbridgeEntry::where('status', 'approved')->count();
        $completedCount = WeighbridgeEntry::where('status', 'completed')->count();
        $rejectedCount = WeighbridgeEntry::where('status', 'rejected')->count();
        $cardOutline = "";

        $entriesQuery = WeighbridgeEntry::with(['owner', 'buyer', 'membership']);

        if ($status) {
            $entriesQuery->where('status', $status);

            switch ($status) {
                case 'pending':
                    $cardOutline = " card-outline card-warning ";
                    break;
                case 'approved':
                    $cardOutline = " card-outline card-primary ";
                    break;
                case 'completed':
                    $cardOutline = " card-outline card-success ";
                    break;
                case 'rejected':
                    $cardOutline = " card-outline card-danger ";
                    break;
                default:
                    // If no specific status is provided, get all entries
                    break;
            }

        }
    
        $entries = $entriesQuery->get();
    
        // Return the view with the entries and status counts
        return view('weighbridge_entries.index', compact('entries', 'pendingCount', 'approvedCount', 'rejectedCount', 'cardOutline'));
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
            'tare_weight' => 'required|numeric|min:0|gte:' . $WeighbridgeEntry->initial_weight,
        ]);

        $WeighbridgeEntry->tare_weight   = $request->tare_weight;
        $WeighbridgeEntry->bag_price = 50;
        $WeighbridgeEntry->status = 'approved';
        $WeighbridgeEntry->save();

        return redirect()->route('weighbridge_entries.show', $WeighbridgeEntry->id)
            ->with('success', 'Tare weight updated successfully.');
    }

}
