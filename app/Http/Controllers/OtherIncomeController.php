<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\IncomeCategory;
use App\Models\JournalDetail;
use App\Models\JournalEntry;
use App\Models\Ledger;
use App\Models\OtherIncome;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OtherIncomeController extends Controller
{
    public function index()
    {
        $otherIncomes = OtherIncome::with('incomeCategory', 'buyer')->get();
        return view('other_incomes.index', compact('otherIncomes'));
    }

    public function create()
    {
        $incomeCategories = IncomeCategory::all();
        $ledgers = Ledger::where('sub_account_group_id', 27)->get();
        $buyers           = Buyer::all();
        return view('other_incomes.create', compact('incomeCategories', 'ledgers', 'buyers'));
    }

    public function store(Request $request)
    {
        if ($request->input('buyer_id') === 'walkin') {
            $request->merge(['buyer_id' => null]);
        }

        $validated = $request->validate([
            'buyer_id' => 'nullable|exists:buyers,id|required_without:name',
            'name' => 'nullable|string|max:255|required_without:buyer_id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['received_date'] = date("Y-m-d"); 
        $validated['income_category_id'] = $request->input('ledger_id'); 
      
        if (!$request->input('buyer_id')) {
            $validated['name'] = $validated['name'];
        }

        OtherIncome::create($validated);

        $journal = JournalEntry::create([
            'journal_date' => Carbon::now()->toDateString(), // YYYY-MM-DD
            'description' => 'Other Income',
        ]);

        $details = [
            [
                'journal_id' => $journal->id,
                'ledger_id' => 10,
                'sub_ledger_id' => 100,
                'debit_amount' => $validated['amount'],
                'credit_amount' => null,
                'description' => '',
            ],
            [
                'journal_id' => $journal->id,
                'ledger_id' => $request->input('ledger_id'),
                'sub_ledger_id' => null,
                'debit_amount' => null,
                'credit_amount' => $validated['amount'],
                'description' => '',
            ],
        ];

        JournalDetail::insert($details);

        return redirect()->route('other_incomes.create')->with('success', 'Other Income record created successfully.');
    }
}
