<?php

namespace App\Http\Controllers;

use App\Models\AccountGroup;
use Illuminate\Http\Request;
use App\Models\Ledger;

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

}
