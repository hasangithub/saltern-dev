<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\SubLedger;
use Illuminate\Http\Request;

class SubLedgerController extends Controller
{
    public function index()
    {
        // Example: Pass a list of SubAccountGroups to the index view
        $subAccountGroups = SubLedger::all();
       // return view('sub_account_groups.index', compact('subAccountGroups'));
    }

    public function create()
    {
        $ledgers = Ledger::all();
        return view('sub_ledgers.create', compact('ledgers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ledger_id' => 'required|exists:account_groups,id',
            'name' => 'required|string|max:255|unique:sub_account_groups,name',
        ]);

        SubLedger::create([
            'ledger_id' => $request->ledger_id,
            'name' => $request->name,
        ]);

        return redirect()->route('sub-ledgers.create')->with('success', 'Sub-Ledger created successfully');
    }

    public function getSubledgers($ledgerId)
{
    $subledgers = Subledger::where('ledger_id', $ledgerId)->get();
    return response()->json($subledgers);
}


}
