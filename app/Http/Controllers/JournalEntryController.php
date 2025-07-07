<?php

namespace App\Http\Controllers;

use App\Models\JournalDetail;
use App\Models\JournalEntry;
use App\Models\Ledger;
use App\Models\SubAccountGroup;
use Illuminate\Http\Request;
use App\Models\SubLedger;
use Illuminate\Support\Facades\DB;

class JournalEntryController extends Controller
{
    public function index()
    {
        $journalEntries = JournalEntry::where('is_reversal', 1)->latest()->get();
        return view('journal_entries.index', compact('journalEntries'));
    }

    public function create()
    {
        // $subLedgers = SubLedger::all();
        // return view('journal_entries.create', compact('subLedgers'));
        $subAccounts = SubAccountGroup::all();
        $ledgers = Ledger::all();
        return view('journal_entries.test1', compact('ledgers', 'subAccounts'));
    }

    public function store(Request $request)
    {

       
    // Validate the request
    $request->validate([
        'details' => 'required|array|min:2',
        'details.*.debit' => 'nullable|numeric|min:0|required_without:details.*.credit',
        'details.*.credit' => 'nullable|numeric|min:0|required_without:details.*.debit',
    ], [
        'details.*.debit.required_without' => 'The debit field is required when credit is not filled.',
        'details.*.credit.required_without' => 'The credit field is required when debit is not filled.',
    ]);

    $totalDebit = 0;
$totalCredit = 0;

foreach ($request->details as $detail) {
    $totalDebit += floatval($detail['debit'] ?? 0);
    $totalCredit += floatval($detail['credit'] ?? 0);
}

if (round($totalDebit, 2) !== round($totalCredit, 2)) {
    return back()->withErrors(['total_mismatch' => 'Total debit must equal total credit.'])->withInput();
}

    // Start a transaction
    DB::beginTransaction();

    try {
        // Create the journal entry
        $journalEntry = JournalEntry::create([
            'journal_date' => date("Y-m-d"),
            'is_reversal' => 1,
            'description' => $request->details[0]['description'] ?? null
        ]);

        // Create journal entry details
        foreach ($request->details as $detail) {

            if (!empty($detail['debit']) && empty($detail['credit'])) {
                $detail['credit'] = 0.00;
            }
    
            // If credit exists, set debit to 0.00
            if (!empty($detail['credit']) && empty($detail['debit'])) {
                $detail['debit'] = 0.00;
            }
            
            JournalDetail::create([
                'journal_id' => $journalEntry->id,
                'ledger_id' => $detail['ledger'],
                'sub_ledger_id' => $detail['subledger'],
                'debit_amount' => $detail['debit'],
                'credit_amount' => $detail['credit'],
                'description'   => $detail['description']
            ]);
        }

        // Commit the transaction
        DB::commit();

        return response()->json(['success' => true,'message' => 'Transaction saved successfully.'], 200);

        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();
            return response()->json(['message' => 'Error saving transaction: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $journalDetails = JournalEntry::with('details')->findOrFail($id);
        return view('journal_entries.show', compact('journalDetails'));
    }

}
