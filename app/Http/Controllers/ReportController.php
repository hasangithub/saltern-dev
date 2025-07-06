<?php

namespace App\Http\Controllers;

use App\Models\AccountGroup;
use App\Models\Buyer;
use App\Models\JournalDetail;
use Illuminate\Http\Request;
use App\Models\Ledger;
use App\Models\Membership;
use App\Models\Saltern;
use App\Models\SubLedger;
use App\Models\WeighbridgeEntry;
use App\Models\Yahai;

class ReportController extends Controller
{
    public function trialBalance()
    {
        $accountGroups = AccountGroup::with([
            'subAccountGroups.ledgers.journalDetails'
        ])->get();
    
        $trialData = [];
    
        foreach ($accountGroups as $group) {
            foreach ($group->subAccountGroups as $subGroup) {
                foreach ($subGroup->ledgers as $ledger) {
    
                    $debit = $ledger->journalDetails->sum('debit_amount');
                    $credit = $ledger->journalDetails->sum('credit_amount');
    
                    $balance = $debit - $credit;
    
                    // Initialize both to zero
                    $adjustedDebit = 0;
                    $adjustedCredit = 0;
    
                    switch (strtolower($group->name)) {
                        case 'assets':
                            if ($balance > 0) {
                                $adjustedDebit = $balance;
                            } else {
                                $adjustedCredit = abs($balance);
                            }
                            break;
    
                        case 'liability':
                        case 'income':
                        case 'expenses':
                            if ($balance < 0) {
                                $adjustedCredit = abs($balance);
                            } else {
                                $adjustedDebit = $balance;
                            }
                            break;
    
                        default:
                            // Fallback to standard logic
                            $adjustedDebit = $debit;
                            $adjustedCredit = $credit;
                            break;
                    }
    
                    if ($adjustedDebit != 0 || $adjustedCredit != 0) {
                        $trialData[] = [
                            'group' => $group->name,
                            'sub_group' => $subGroup->name,
                            'ledger' => $ledger->name,
                            'debit' => $adjustedDebit,
                            'credit' => $adjustedCredit,
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

    $subAccountGroups = \App\Models\SubAccountGroup::all();

    return view('reports.ledger.index', compact('entries', 'subAccountGroups', 'from', 'to', 'ledgerId'));
}

public function generateLedger(Request $request)
{
    $request->validate([
        'from_date' => 'required|date',
        'to_date' => 'required|date',
        'ledger_id' => 'nullable|exists:ledgers,id',
        'sub_ledger_id' => 'nullable|exists:sub_ledgers,id',
    ]);

    $ledgerId = $request->ledger_id;
    $subLedgerId = $request->sub_ledger_id;
    $fromDate = $request->from_date;
    $toDate = $request->to_date;

    // If sub-ledger is selected, show its detail (Case 2)
    if ($subLedgerId) {
        $subLedger = SubLedger::with('ledger')->findOrFail($subLedgerId);
        $ledger = $subLedger->ledger;

        $journalDetails = JournalDetail::with('journalEntry')
            ->where('ledger_id', $ledger->id)
            ->where('sub_ledger_id', $subLedgerId)
            ->whereHas('journalEntry', fn($q) => $q->whereBetween('journal_date', [$fromDate, $toDate]))
            ->get();

        return view('reports.ledger.result', compact('journalDetails', 'ledger', 'subLedger', 'fromDate', 'toDate'));
    }

    // If only ledger is selected
    if ($ledgerId) {
        $ledger = Ledger::with('subLedgers')->findOrFail($ledgerId);
        $hasSubLedgers = $ledger->subLedgers->isNotEmpty();

        // Case 1: Ledger with subledgers — show summary per subledger
        if ($hasSubLedgers) {
            $subLedgerSummaries = [];

            foreach ($ledger->subLedgers as $sl) {
                $details = JournalDetail::with('journalEntry')
                    ->where('ledger_id', $ledgerId)
                    ->where('sub_ledger_id', $sl->id)
                    ->whereHas('journalEntry', fn($q) => $q->whereBetween('journal_date', [$fromDate, $toDate]))
                    ->get();

                $debit = $details->sum('debit_amount');
                $credit = $details->sum('credit_amount');

                if ($debit != 0 || $credit != 0) {
                    $subLedgerSummaries[] = [
                        'sub_ledger' => $sl,
                        'debit' => $debit,
                        'credit' => $credit,
                    ];
                }
            }

            return view('reports.ledger.summary', compact('subLedgerSummaries', 'ledger', 'fromDate', 'toDate'));

        } else {
            // Case 3: Ledger has NO subledgers — show summary for that ledger only
            $journalDetails = JournalDetail::with('journalEntry')
                ->where('ledger_id', $ledgerId)
                ->whereNull('sub_ledger_id')
                ->whereHas('journalEntry', fn($q) => $q->whereBetween('journal_date', [$fromDate, $toDate]))
                ->get();

                return view('reports.ledger.summary', compact('journalDetails', 'ledger', 'fromDate', 'toDate'));
        }

      
    }

    // If nothing selected, return error or redirect back
    return back()->withErrors('Please select a ledger or subledger.');
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
        $salterns = Saltern::with('memberships.owner')
        ->where('yahai_id', $request->yahai_id)
        ->whereHas('memberships') // ensures only those with active membership are included
        ->get();

            return response()->json(['salterns' => $salterns]);
    }

    public function getLedgers(Request $request)
    {
        $ledgers = Ledger::where('sub_account_group_id', $request->sub_account_id)->get();
        return response()->json(['ledgers' => $ledgers]);
    }

    public function getSubLedgers(Request $request)
    {
        $subLedgers = SubLedger::where('ledger_id', $request->ledger_id)->get();
        return response()->json(['subLedgers' => $subLedgers]);
    }
}