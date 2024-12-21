<?php

namespace App\Http\Controllers;

use App\Models\AccountGroup;
use Illuminate\Http\Request;

class AccountGroupController extends Controller
{
    public function index()
    {
        $accounts = AccountGroup::with('subAccountGroups.ledgers.subLedgers')->get();

        return view('account_groups.index', compact('accounts'));
    }
}
