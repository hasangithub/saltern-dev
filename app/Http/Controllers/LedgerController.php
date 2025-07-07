<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\SubAccountGroup;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function index()
    {
        // Example: Pass a list of SubAccountGroups to the index view
        $subAccountGroups = SubAccountGroup::all();
       // return view('sub_account_groups.index', compact('subAccountGroups'));
    }

    public function create()
    {
        $subAccountGroups = SubAccountGroup::all();
        return view('ledgers.create', compact('subAccountGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sub_account_group_id' => 'required|exists:sub_account_groups,id',
            'name'                 => 'required|string|max:255',
        ]);

        Ledger::create([
            'sub_account_group_id' => $request->sub_account_group_id,
            'name' => $request->name,
        ]);

        return redirect()->route('ledgers.create')->with('success', 'Ledger '. $request->name .' created successfully');
    }

    public function getBySubAccount($subAccountId)
    {
        return Ledger::where('sub_account_group_id', $subAccountId)
            ->select('id', 'name')
            ->get();
    }

}
