<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    public function index()
    {
        $places = Place::all();
        return view('places.index', compact('places'));
    }

    public function create()
    {
        return view('places.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:places,name|max:255',
            'description' => 'nullable|string',
        ]);

        Place::create($validated);

        return redirect()->route('places.index')->with('success', 'Place created successfully.');
    }

    public function edit(Place $place)
    {
        return view('places.edit', compact('place'));
    }

    public function update(Request $request, Place $place)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:places,name,' . $place->id,
            'description' => 'nullable|string',
        ]);

        $place->update($validated);

        return redirect()->route('places.index')->with('success', 'Place updated successfully.');
    }

    public function destroy(Place $place)
    {
        $place->delete();
        return redirect()->route('places.index')->with('success', 'Place deleted successfully.');
    }
}
