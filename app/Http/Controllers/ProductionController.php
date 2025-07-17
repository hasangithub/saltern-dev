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
    
        // Apply filtering if a saltern_id is selected
        $query = WeighbridgeEntry::with(['owner', 'membership'])->where('owner_id', $ownerId);
    
        if ($request->filled('saltern_id')) {
            $query->whereHas('membership', function ($q) use ($request) {
                $q->where('saltern_id', $request->saltern_id);
            });
        }

        if ($request->has('start_date') && $request->has('end_date') && !is_null($request->start_date) && $request->start_date !== ''  && !is_null($request->end_date) && $request->end_date !== '') {
            $query->whereBetween('created_at', [
                $request->start_date,
                $request->end_date,
            ]);
        }
    
        $productions = $query->get();

        return view('productions.index', compact('productions', 'salterns'));
    }
}
