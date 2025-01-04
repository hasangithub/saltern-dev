<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('category')->get();
       
        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        $categories = ExpenseCategory::all();
        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // Validate the expense data
        $request->validate([
            'amount' => 'required|numeric',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        // Create a new expense record
        Expense::create($request->all());

        return redirect()->route('expenses.create')->with('success', 'Expense created successfully.');
    }

    public function edit(Expense $expense)
    {
        $categories = ExpenseCategory::all();
        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        // Validate the expense data
        $request->validate([
            'amount' => 'required|numeric',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);

        // Update the expense record
        $expense->update($request->all());

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        // Delete the expense record
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }
}
