<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use App\Models\WeighbridgeEntry;
use Illuminate\Http\Request;

class ProductionController extends Controller
{
    public function index(Request $request)
    {
        //$ownerId = auth()->user()->id; // Assuming owner authentication is used
        $salterns = Membership::where('owner_id', 2)->get();
    
        // Apply filtering if a saltern_id is selected
        $query = WeighbridgeEntry::with(['owner', 'membership'])->where('owner_id', 2);
    
        if ($request->filled('saltern_id')) {
            $query->where('membership_id', $request->saltern_id);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date,
                $request->end_date,
            ]);
        }
    
        $productions = $query->get();

        return view('productions.index', compact('productions', 'salterns'));
    }
}
