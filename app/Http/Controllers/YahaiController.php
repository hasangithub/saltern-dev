<?php

namespace App\Http\Controllers;

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
        return view('yahai.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $request->validate([
            'name' => 'required|string|max:255',
            'side' => 'required|string'
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
        
        return view('yahai.edit', ['yahai' => $yahai]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Yahai $yahai)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'side' => 'required|string'
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
