<?php

namespace App\Http\Controllers;

use App\Models\Side;
use App\Models\Yahai;
use Illuminate\Http\Request;

class YahaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $yahais = Yahai::query()->orderBy('name','asc')->get(); // Retrieve all yahais
        return view('yahai.index', compact('yahais'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sides = Side::all(); 
        return view('yahai.create', compact('sides')); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $request->validate([
            'name' => 'required|string|max:255',
            'side_id' => 'required|exists:sides,id'
        ]);

        Yahai::create($request->all());

        return redirect()->route('yahai.index')->with('success', 'Yahai created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Yahai $yahai)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Yahai $yahai)
    {
        $sides = Side::all(); 
        return view('yahai.edit', ['yahai' => $yahai, 'sides' => $sides]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Yahai $yahai)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'side_id' => 'required|exists:sides,id',
        ]);

        $yahai->update($data);

        return to_route('yahai.index', $yahai)->with('success', 'Yahai was updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Yahai $yahai)
    {
        $yahai->delete();
        return to_route('yahai.index', $yahai)->with('success', 'Yahai was Deleted');
    }
}
