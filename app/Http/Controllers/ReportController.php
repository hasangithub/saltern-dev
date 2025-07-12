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
            'subAccountGroups.ledgers' => function ($q) {
                $q->with([
                    'journalDetails', // only direct (no subledger_id)
                    'subLedgers.journalDetails'
                ]);
            }
        ])->get();
    
        $trialData = [];
        $totalDebit = 0;
        $totalCredit = 0;
    
        foreach ($accountGroups as $group) {
            foreach ($group->subAccountGroups as $subGroup) {
                foreach ($subGroup->ledgers as $ledger) {
    
                    // 1. Ledger own entries (excluding subledger)
                    $ledgerDebit = $ledger->journalDetails->sum('debit_amount');
                    $ledgerCredit = $ledger->journalDetails->sum('credit_amount');
    
                    // 2. Subledgers entries
                    $subRows = [];
                    $subDebitTotal = 0;
                    $subCreditTotal = 0;
    
                    foreach ($ledger->subLedgers as $sub) {
                        $sd = $sub->journalDetails->sum('debit_amount');
                        $sc = $sub->journalDetails->sum('credit_amount');
                        $subBalance = $sd - $sc;
                        [$subDebit, $subCredit] = $this->adjustBalance($group->name, $subBalance);
    
                        if ($subDebit != 0 || $subCredit != 0) {
                            $subRows[] = [
                                'group' => $group->name,
                                'sub_group' => $subGroup->name,
                                'ledger' => '↳ ' . $sub->name,
                                'debit' => $subDebit,
                                'credit' => $subCredit,
                                'is_sub' => true,
                            ];
                        }
    
                        $subDebitTotal += $sd;
                        $subCreditTotal += $sc;
                    }
    
                    // 3. Ledger total including subledgers
                    $totalLedgerDebit = $ledgerDebit + $subDebitTotal;
                    $totalLedgerCredit = $ledgerCredit + $subCreditTotal;
                    $balance = $totalLedgerDebit - $totalLedgerCredit;
                    [$adjDebit, $adjCredit] = $this->adjustBalance($group->name, $balance);
    
                    if ($adjDebit != 0 || $adjCredit != 0) {
                        $trialData[] = [
                            'group' => $group->name,
                            'sub_group' => $subGroup->name,
                            'ledger' => $ledger->name,
                            'debit' => $adjDebit,
                            'credit' => $adjCredit,
                            'is_sub' => false,
                        ];
    
                        $totalDebit += $adjDebit;
                        $totalCredit += $adjCredit;
                    }
    
                    // 4. Append subledger rows
                    foreach ($subRows as $row) {
                        $trialData[] = $row;
                    }
                }
            }
        }
    
        return view('reports.trial_balance', compact('trialData', 'totalDebit', 'totalCredit'));
    }
    
    
    

    
    private function adjustBalance($groupName, $balance)
    {
        $debit = 0;
        $credit = 0;
    
        switch (strtolower($groupName)) {
            case 'assets':
                $balance >= 0 ? $debit = $balance : $credit = abs($balance);
                break;
            case 'liability':
            case 'income':
            case 'expenses':
                $balance < 0 ? $credit = abs($balance) : $debit = $balance;
                break;
            default:
                $debit = $balance >= 0 ? $balance : 0;
                $credit = $balance < 0 ? abs($balance) : 0;
        }
    
        return [$debit, $credit];
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

    $fromDate = $request->from_date;
    $toDate = $request->to_date;
    $ledgerId = $request->ledger_id;
    $subLedgerId = $request->sub_ledger_id;

    // If subledger selected
    if ($subLedgerId) {
        $subLedger = SubLedger::with('ledger')->findOrFail($subLedgerId);
        $ledger = $subLedger->ledger;

        $opening = $this->calculateOpeningBalance($ledger->id, $subLedgerId, $fromDate);
        $journalDetails = $this->getJournalDetails($ledger->id, $subLedgerId, $fromDate, $toDate);

        return view('reports.ledger.result', compact('ledger', 'subLedger', 'opening', 'journalDetails', 'fromDate', 'toDate'));
    }

    // If ledger selected
    if ($ledgerId) {
        $ledger = Ledger::with('subLedgers')->findOrFail($ledgerId);

        // Ledger has subledgers — show per subledger details
        if ($ledger->subLedgers->isNotEmpty()) {
            $subLedgerSummaries = [];

            foreach ($ledger->subLedgers as $sub) {
                $opening = $this->calculateOpeningBalance($ledgerId, $sub->id, $fromDate);
                $journalDetails = $this->getJournalDetails($ledgerId, $sub->id, $fromDate, $toDate);

                // Only add subledger if there's some balance or transactions
                if ($opening['balance'] != 0 || $journalDetails->isNotEmpty()) {
                    $subLedgerSummaries[] = [
                        'sub_ledger' => $sub,
                        'opening' => $opening,
                        'journalDetails' => $journalDetails,
                    ];
                }
            }

            return view('reports.ledger.subledger_summary', compact('ledger', 'subLedgerSummaries', 'fromDate', 'toDate'));
        }

        // Ledger has no subledgers — show ledger transactions
        $opening = $this->calculateOpeningBalance($ledgerId, null, $fromDate);
        $journalDetails = $this->getJournalDetails($ledgerId, null, $fromDate, $toDate);

        return view('reports.ledger.summary', compact('ledger', 'opening', 'journalDetails', 'fromDate', 'toDate'));
    }

    return back()->withErrors('Please select a ledger or subledger.');
}

private function calculateOpeningBalance($ledgerId, $subLedgerId = null, $beforeDate)
{
    $query = JournalDetail::where('ledger_id', $ledgerId)
        ->whereHas('journalEntry', fn($q) => $q->where('journal_date', '<', $beforeDate));

    if ($subLedgerId) {
        $query->where('sub_ledger_id', $subLedgerId);
    } else {
        $query->whereNull('sub_ledger_id');
    }

    $debit = $query->sum('debit_amount');
    $credit = $query->sum('credit_amount');
    $balance = $debit - $credit;

    return [
        'debit' => $debit,
        'credit' => $credit,
        'balance' => $balance,
    ];
}

private function getJournalDetails($ledgerId, $subLedgerId = null, $fromDate, $toDate)
{
    $query = JournalDetail::with('journalEntry')
        ->where('ledger_id', $ledgerId)
        ->whereHas('journalEntry', fn($q) =>
            $q->whereBetween('journal_date', [$fromDate, $toDate])
        );

    if ($subLedgerId) {
        $query->where('sub_ledger_id', $subLedgerId);
    } else {
        $query->whereNull('sub_ledger_id');
    }

    return $query->get()
        ->sortBy(fn($d) => optional($d->journalEntry)->journal_date ?? '2100-01-01')
        ->values(); // Re-index for blade use
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