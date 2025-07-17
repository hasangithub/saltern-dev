<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\WeighbridgeEntry;
use Illuminate\Http\Request;

class ProductionController extends Controller
{
    public function index(Request $request)
    {
        $ownerId =auth('owner')->id(); 
        $salterns = Membership::where('owner_id', $ownerId)->get();
    
        $query = WeighbridgeEntry::with(['owner', 'membership']);

    // When saltern_id is selected
    if ($request->filled('saltern_id')) {
        $query->whereHas('membership', function ($q) use ($ownerId, $request) {
            $q->where('owner_id', $ownerId)
              ->where('saltern_id', $request->saltern_id);
        });
    } else {
        // When saltern_id is NOT selected, just filter by owner's memberships
        $query->whereHas('membership', function ($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        });
    }

    // Optional date filtering
    if ($request->has('start_date') && $request->has('end_date') && !empty($request->start_date) && !empty($request->end_date)) {
        $query->whereBetween('created_at', [
            $request->start_date,
            $request->end_date,
        ]);
    }

    $productions = $query->get();
dd($productions);
        return view('productions.index', compact('productions', 'salterns'));
    }
}
