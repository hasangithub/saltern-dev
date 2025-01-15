<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\Membership;
use App\Models\Owner;
use App\Models\Saltern;
use App\Models\Side;
use App\Models\WeighbridgeEntry;
use App\Models\Yahai;
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
        $sides  = Side::all();
        $owners = Owner::all();
        $buyers = Buyer::all();
        $memberships = Membership::all();
        $nextSerialNo = WeighbridgeEntry::max('id') + 1;

        return view('weighbridge_entries.create', compact('owners', 'buyers', 'memberships', 'nextSerialNo', 'sides'));
    }

    public function store(Request $request)
    {
       $validated=  $request->validate([
            'vehicle_id' => 'required|string',
            'initial_weight' => 'required|numeric',
            'tare_weight' => 'required|numeric|min:0|gte:initial_weight',
            'transaction_date' => 'nullable|date',
            'membership_id' => 'required|exists:memberships,id',
            'buyer_id' => 'required|exists:buyers,id',
        ]);

        $membership = Membership::findOrFail($request->membership_id);
        $data = $request->all();
        $data['owner_id'] = $membership->owner_id;
        $data['transaction_date'] = $validated['transaction_date'] ?? date("Y-m-d");
        $data['bag_price'] = 50;
        $data['status'] = 'approved';

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

    public function getYahais(Request $request)
    {
        $yahais = Yahai::where('side_id', $request->side_id)->get();
        return response()->json(['yahais' => $yahais]);
    }

    public function getSalterns(Request $request)
    {
        $salterns = Saltern::where('yahai_id', $request->yahai_id)->get();
        return response()->json(['salterns' => $salterns]);
    }

    public function getMembershipDetails($saltern_id)
    {
        $membership = Membership::where('saltern_id', $saltern_id)
            ->with('owner')  // Eager load the owner
            ->first();

        if ($membership) {
            return response()->json([
                'status' => 'success',
                'membership' => $membership,
                'owner' => $membership->owner,  // Include the owner details in the response
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'No membership found for this saltern'
        ]);
    }
}
