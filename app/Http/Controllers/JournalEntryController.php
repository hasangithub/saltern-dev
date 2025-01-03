<?php

namespace App\Http\Controllers;

use App\Models\JournalDetail;
use App\Models\JournalEntry;
use App\Models\Ledger;
use Illuminate\Http\Request;
use App\Models\SubLedger;
use Illuminate\Support\Facades\DB;

class JournalEntryController extends Controller
{
    public function index()
    {
        $journalDetails = JournalDetail::all();
        return view('journal_entries.index', compact('journalDetails'));
    }

    public function create()
    {
        // $subLedgers = SubLedger::all();
        // return view('journal_entries.create', compact('subLedgers'));

        $ledgers = Ledger::all();
        return view('journal_entries.test1', compact('ledgers'));
    }

    public function store(Request $request)
    {

       
    // Validate the request
    $request->validate([
        //'details' => 'required|array|size:2', // Require exactly 2 details
        'details.*.debit' => 'nullable|numeric|min:0|required_without:details.*.credit',
        'details.*.credit' => 'nullable|numeric|min:0|required_without:details.*.debit',
    ], [
        'details.*.debit.required_without' => 'The debit field is required when credit is not filled.',
        'details.*.credit.required_without' => 'The credit field is required when debit is not filled.',
    ]);

    // Start a transaction
    DB::beginTransaction();

    try {
        // Create the journal entry
        $journalEntry = JournalEntry::create([
            'journal_date' => date("Y-m-d"),
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
                'sub_ledger_id' => $detail['subledger'],
                'debit_amount' => $detail['debit'],
                'credit_amount' => $detail['credit'],
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

}
