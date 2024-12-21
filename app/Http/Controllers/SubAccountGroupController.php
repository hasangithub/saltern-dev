<?php

namespace App\Http\Controllers;

use App\Models\SubAccountGroup;
use App\Models\AccountGroup;
use Illuminate\Http\Request;

class SubAccountGroupController extends Controller
{
    public function index()
    {
        $subAccountGroups = SubAccountGroup::all();
       // return view('sub_account_groups.index', compact('subAccountGroups'));
    }

    public function create()
    {
        $accountGroups = AccountGroup::all();
        return view('sub_account_groups.create', compact('accountGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_group_id' => 'required|exists:account_groups,id',
            'name' => 'required|string|max:255|unique:sub_account_groups,name',
        ]);

        SubAccountGroup::create([
            'account_group_id' => $request->account_group_id,
            'name' => $request->name,
        ]);

        return redirect()->route('sub-account-groups.create')->with('success', 'Sub-Account Group ' . $request->name .' created successfully');
    }
}
