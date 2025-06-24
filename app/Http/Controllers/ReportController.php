<?php

namespace App\Http\Controllers;

use App\Models\AccountGroup;
use App\Models\Buyer;
use App\Models\JournalDetail;
use Illuminate\Http\Request;
use App\Models\Ledger;
use App\Models\Membership;
use App\Models\Saltern;
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
                ->when($request->membership_id, function($query) use ($request) {
                    $query->whereHas('membership', function($q) use ($request) {
                        $q->where('id', $request->membership_id);
                    });
                })
                ->when($request->buyer_id, function($query) use ($request) {
                    $query->where('buyer_id', $request->buyer_id);
                })
                ->get();
        }

        return view('reports.production.result', compact('yahaies', 'owners', 'buyers', 'entries'));   
    }

    public function indexLedger(Request $request)
{
    $from = $request->input('from');
    $to = $request->input('to');
    $ledgerId = $request->input('ledger_id');

    $query = JournalDetail::query();

    if ($from) {
        $query->whereDate('entry_date', '>=', $from);
    }
    if ($to) {
        $query->whereDate('entry_date', '<=', $to);
    }
    if ($ledgerId) {
        $query->where('ledger_id', $ledgerId);
    }

    $entries = $query->get();

    // Get list of ledgers for the dropdown
    $ledgers = \App\Models\Ledger::all();

    return view('reports.ledger.index', compact('entries', 'ledgers', 'from', 'to', 'ledgerId'));
}

public function generateLedger(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'ledger_id' => 'required|exists:ledgers,id',
        ]);

        $ledgerId = $request->ledger_id;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        $journalDetails = JournalDetail::with(['ledger', 'subLedger', 'journalEntry'])
            ->where('ledger_id', $ledgerId)
            ->whereHas('journalEntry', function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('journal_date', [$fromDate, $toDate]);
            })
            ->get();

        $ledger = Ledger::find($ledgerId);

        return view('reports.ledger.result', compact('journalDetails', 'ledger', 'fromDate', 'toDate'));
    }

    public function generateBuyerProduction(Request $request)
    {
        $yahaies = Yahai::all();
        $owners = Membership::all();
        $buyers = Buyer::all();
    
        $entries = collect();
    
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $entries = WeighbridgeEntry::with(['buyer', 'membership.owner', 'membership.saltern.yahai'])
                ->whereBetween('transaction_date', [$request->from_date, $request->to_date])
                ->when($request->buyer_id, function($query) use ($request) {
                    $query->where('buyer_id', $request->buyer_id);
                })
                ->get();
        }
        
        return view('reports.production.buyer-result', compact('yahaies', 'owners', 'buyers', 'entries'));   
    }

    public function getSalterns(Request $request)
    {
        $salterns = Saltern::with('activeMembership.owner')
        ->where('yahai_id', $request->yahai_id)
        ->whereHas('activeMembership') // ensures only those with active membership are included
        ->get();

            return response()->json(['salterns' => $salterns]);
    }
}