<?php

namespace App\Http\Controllers;

use App\Models\AccountGroup;
use App\Models\Ledger;
use App\Models\SubAccountGroup;
use App\Models\SubLedger;
use Illuminate\Http\Request;

class AccountGroupController extends Controller
{
    public function index()
    {
        $accounts = AccountGroup::with('subAccountGroups.ledgers.subLedgers')->get();

        return view('account_groups.index', compact('accounts'));
    }

    public function update(Request $request)
{
    $modelMap = [
        'account' => AccountGroup::class,
        'subaccount' => SubAccountGroup::class,
        'ledger' => Ledger::class,
        'subledger' => SubLedger::class,
    ];

    $model = $modelMap[$request->type] ?? null;

    if (!$model) {
        return response()->json(['error' => 'Invalid type'], 400);
    }

    $record = $model::findOrFail($request->id);
    $record->name = $request->name;
    $record->save();

    return response()->json(['status' => 'success']);
}

}
