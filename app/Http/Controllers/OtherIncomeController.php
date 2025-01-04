<?php

namespace App\Http\Controllers;

use App\Models\IncomeCategory;
use App\Models\Ledger;
use App\Models\OtherIncome;
use Illuminate\Http\Request;

class OtherIncomeController extends Controller
{
    public function index()
    {
        $otherIncomes = OtherIncome::with('incomeCategory')->get();
        return view('other_incomes.index', compact('otherIncomes'));
    }

    public function create()
    {
        $incomeCategories = IncomeCategory::all();
        $ledgers           = Ledger::all();
        return view('other_incomes.create', compact('incomeCategories', 'ledgers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'received_date' => 'required|date',
            'income_category_id' => 'required|exists:income_categories,id',
            'amount' => 'required|numeric|min:0',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        OtherIncome::create($validated);

        return redirect()->route('other_incomes.create')->with('success', 'Other Income record created successfully.');
    }
}
