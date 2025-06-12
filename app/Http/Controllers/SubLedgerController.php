<?php

namespace App\Http\Controllers;

use App\Models\AccountGroup;
use App\Models\Ledger;
use App\Models\SubAccountGroup;
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
        $accounts = AccountGroup::all();

        return view('sub_ledgers.create', compact('ledgers', 'accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ledger_id' => 'required|exists:account_groups,id',
            'name' => 'required|string|max:255',
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

    public function getSubAccounts(Request $request)
    {
        $subAccounts = SubAccountGroup::where('account_group_id', $request->account_id)->get();
        return response()->json(['sub_accounts' => $subAccounts]);
    }

    public function getLedgers(Request $request)
    {
        $ledgers = Ledger::where('sub_account_group_id', $request->sub_account_id)->get();
        return response()->json(['ledgers' => $ledgers]);
    }

    public function storeSubLedger(Request $request)
    {
        $rules = [
            'account_id' => 'required|exists:account_groups,id',  // Account validation
            'sub_account_id' => 'required|string',  // Allow string (new tag) or numeric (existing ID)
            'new_sub_account_name' => 'nullable|string|max:255',  // New sub account name validation
            'ledger_id' => 'required|string',  // Allow string (new tag) or numeric (existing ID)
            'new_ledger_name' => 'nullable|string|max:255',  // New ledger name validation
            'sub_ledger_name' => 'required|string|max:255',  // Sub ledger name required
        ];
    
        // Validate based on conditions for existing or new tag
        if ($request->sub_account_id && is_numeric($request->sub_account_id)) {
            // If it's a numeric ID, check if it exists in the sub_account_groups table
            $rules['sub_account_id'] = 'exists:sub_account_groups,id';
        } elseif ($request->sub_account_id && !is_numeric($request->sub_account_id)) {
            // If it's a new tag, validate it as a string (no numeric ID)
            $rules['sub_account_id'] = 'nullable|string|max:255';
        }
    
        if ($request->ledger_id && is_numeric($request->ledger_id)) {
            // If it's a numeric ID, check if it exists in the ledgers table
            $rules['ledger_id'] = 'exists:ledgers,id';
        } elseif ($request->ledger_id && !is_numeric($request->ledger_id)) {
            // If it's a new tag, validate it as a string (no numeric ID)
            $rules['ledger_id'] = 'nullable|string|max:255';
        }
    
        // Validate the request
        $request->validate($rules);
    
        // Create or get sub_account
        if ($request->sub_account_id && !is_numeric($request->sub_account_id)) {
            // Create new sub account if it's a new tag
            $subAccount = SubAccountGroup::create([
                'account_group_id' => $request->account_id,
                'name' => $request->new_sub_account_name, // New sub account name
            ]);
            // Replace sub_account_id with the newly created ID
            $request->merge(['sub_account_id' => $subAccount->id]);
        }
    
        // Create or get ledger
        if ($request->ledger_id && !is_numeric($request->ledger_id)) {
            // Create new ledger if it's a new tag
            $ledger = Ledger::create([
                'sub_account_group_id' => $request->sub_account_id,
                'name' => $request->new_ledger_name, // New ledger name
            ]);
            // Replace ledger_id with the newly created ID
            $request->merge(['ledger_id' => $ledger->id]);
        }
    
        // Create sub ledger entry
        SubLedger::create([
            'ledger_id' => $request->ledger_id,
            'name' => $request->sub_ledger_name,
        ]);
    
        return redirect()->back()->with('success', 'Sub Ledger created successfully!');
    
    }
}
