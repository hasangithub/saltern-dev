<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index()
    {
        $inventories = Inventory::with('replacedInventory')->get();
        return view('inventory.index', compact('inventories'));
    }

    public function create()
    {
        $inventories = Inventory::all(); // for replaced reference
        $places = Place::all();
        return view('inventory.create', compact('inventories', 'places'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stock_code' => 'nullable|string|max:100',
            'qty' => 'nullable|numeric',
            'date_of_purchase' => 'nullable|date',
            'place_id' => 'nullable|exists:places,id',
            'warranty_from' => 'nullable|date',
            'warranty_to' => 'nullable|date|after_or_equal:warranty_from',
            'amount' => 'required|numeric',
            'voucher_id' => 'nullable|integer',
            'description' => 'nullable|string',
            'status' => 'required|in:inuse,repaired,replaced',
            'replaced_id' => 'nullable|exists:inventories,id',
        ]);

        $validated['created_by'] = Auth::id();

        Inventory::create($validated);

        return redirect()->route('inventories.index')->with('success', 'Inventory added successfully');
    }

    public function edit(Inventory $inventory)
    {
        $inventories = Inventory::all();
        $places = Place::all();
        return view('inventory.edit', compact('inventory', 'inventories', 'places'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stock_code' => 'nullable|string|max:100',
            'qty' => 'nullable|numeric',
            'date_of_purchase' => 'nullable|date',
            'place_id' => 'nullable|exists:places,id',
            'warranty_from' => 'nullable|date',
            'warranty_to' => 'nullable|date|after_or_equal:warranty_from',
            'amount' => 'required|numeric',
            'voucher_id' => 'nullable|integer',
            'description' => 'nullable|string',
            'status' => 'required|in:inuse,repaired,replaced',
            'replaced_id' => 'nullable|exists:inventories,id',
        ]);

        $inventory->update($validated);

        return redirect()->route('inventories.index')->with('success', 'Inventory updated successfully');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return redirect()->route('inventories.index')->with('success', 'Inventory deleted successfully');
    }
}
