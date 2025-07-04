<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use App\Models\Saltern;
use App\Models\Yahai;
use Illuminate\Http\Request;

class SalternController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $salterns = Saltern::all();
        return view('saltern.index', compact('salterns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $yahais = Yahai::all();

        return view('saltern.create', compact('yahais'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'yahai_id' => 'required|exists:yahai,id',
            'name' => 'required|string|max:255'
        ]);

        Saltern::create($request->all());

        return redirect()->route('saltern.index')->with('success', 'Saltern created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Saltern $saltern)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Saltern $saltern)
    {
        $yahais = Yahai::all();
        return view('saltern.edit', ['saltern'=>$saltern, 'yahais'=>$yahais]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Saltern $saltern)
    {
        $data = $request->validate([
            'yahai_id' => 'required|exists:yahai,id',
            'name' => 'required|string|max:255'
        ]);

        $saltern->update($data);

        return to_route('saltern.index', $saltern)->with('success', 'Saltern was updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Saltern $saltern)
    {
        $saltern->delete();

        return to_route('saltern.index')->with('success', 'Saltern was deleted.');
    }

    function getByYahai($yahaiId)
    {
        $salterns = Saltern::where('yahai_id', $yahaiId)->get();
        return response()->json($salterns);
    }
}
