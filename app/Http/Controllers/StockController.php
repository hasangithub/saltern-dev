<?php

namespace App\Http\Controllers;

use App\Models\StockItem;
use App\Models\StockTransaction;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $items = StockItem::all();
        return view('stock.index', compact('items'));
    }

    public function storeItem(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'opening_balance' => 'required|numeric|min:0',
        ]);

        StockItem::create($request->only('name', 'opening_balance'));

        return back()->with('success', 'Item added successfully.');
    }

    public function showItem($id)
    {
        $item = StockItem::findOrFail($id);
        $transactions = $item->transactions()->orderBy('transaction_date')->get();
        $opening  = $item->opening_balance;
        return view('stock.show', compact('item', 'transactions', 'opening'));
    }

    public function storeTransaction(Request $request, $itemId)
    {
        $request->validate([
            'type' => 'required|in:purchase,issue',
            'quantity' => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
        ]);

        StockTransaction::create([
            'stock_item_id' => $itemId,
            'type' => $request->type,
            'quantity' => $request->quantity,
            'transaction_date' => $request->transaction_date,
        ]);

        return back()->with('success', ucfirst($request->type) . ' recorded successfully.');
    }
}
