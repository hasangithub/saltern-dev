<?php

namespace App\Http\Controllers;

use App\Models\AccountGroup;
use App\Models\Buyer;
use Illuminate\Http\Request;
use App\Models\Ledger;
use App\Models\Membership;
use App\Models\WeighbridgeEntry;
use App\Models\Yahai;

class ReportController extends Controller
{
    public function trialBalance()
{
    $accountGroups = AccountGroup::with([
        'subAccountGroups.ledgers.subLedgers.journalDetails'
    ])->get();

    $trialData = [];

    foreach ($accountGroups as $group) {
        foreach ($group->subAccountGroups as $subGroup) {
            foreach ($subGroup->ledgers as $ledger) {
                $debit = 0;
                $credit = 0;

                     $debit += $ledger->journalDetails->sum('debit_amount');
                     $credit += $ledger->journalDetails->sum('credit_amount');

                if ($debit != 0 || $credit != 0) {
                    $trialData[] = [
                        'group' => $group->name,
                        'sub_group' => $subGroup->name,
                        'ledger' => $ledger->name,
                        'debit' => $debit,
                        'credit' => $credit,
                    ];
                }
            }
        }
    }

    $totalDebit = collect($trialData)->sum('debit');
    $totalCredit = collect($trialData)->sum('credit');

    return view('reports.trial_balance', [
        'trialData' => $trialData,
        'totalDebit' => $totalDebit,
        'totalCredit' => $totalCredit,
    ]);
}

public function indexProduction()
{
    $yahaies = Yahai::all();
    $owners = Membership::all();
    $buyers = Buyer::all();

    return view('reports.production.index', compact('yahaies', 'owners', 'buyers'));
}

    public function generateProduction(Request $request)
    {
        $yahaies = Yahai::all();
        $owners = Membership::all();
        $buyers = Buyer::all();
    
        $entries = collect();
    
        if ($request->filled('from_date') && $request->filled('to_date') && $request->filled('yahai_id')) {
            $entries = WeighbridgeEntry::with(['buyer', 'membership.owner', 'membership.saltern.yahai'])
                ->whereBetween('transaction_date', [$request->from_date, $request->to_date])
                ->whereHas('membership.saltern', function($query) use ($request) {
                    $query->where('yahai_id', $request->yahai_id);
                })
                ->when($request->owner_id, function($query) use ($request) {
                    $query->whereHas('membership', function($q) use ($request) {
                        $q->where('owner_id', $request->owner_id);
                    });
                })
                ->when($request->buyer_id, function($query) use ($request) {
                    $query->where('buyer_id', $request->buyer_id);
                })
                ->get();
        }

        return view('reports.production.result', compact('yahaies', 'owners', 'buyers', 'entries'));   
    }

}
