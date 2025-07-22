<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WeighbridgeEntry;
use Yajra\DataTables\Facades\DataTables;

class WeighbridgeTestController extends Controller
{
   
    
    public function index()
    {
        return view('weighbridge_entries.test');
    }

    public function data()
    {
        $entries = WeighbridgeEntry::with([
            'buyer', 'membership.owner', 'membership.saltern.yahai', 'loanRepayments', 'receipt'
        ])->orderBy('transaction_date')->orderBy('created_at');
    
        return DataTables::eloquent($entries)
            ->addColumn('turn_no', fn($entry) => optional($entry)->turn_no)
            ->addColumn('buyer_name', fn($entry) => optional($entry->buyer)->full_name)
            ->addColumn('owner_name', fn($entry) => optional($entry->membership->owner)->name_with_initial)
            ->addColumn('yahai_name', fn($entry) => optional($entry->membership->saltern->yahai)->name)
            ->addColumn('waikal', fn($entry)     => optional($entry->membership->saltern)->name)
            ->addColumn('net_weight', fn($entry) => $entry->net_weight)
            ->addColumn('bags', fn($entry) => $entry->bags_count)
            ->addColumn('amount', fn($entry) => number_format($entry->total_amount, 2))
            ->addColumn('receipt', function ($entry) {
                if ($entry->receipt) {
                    return '<a href="' . route('receipts.show', $entry->receipt->id) . '" target="_blank">
                                <span class="badge bg-success">#' . $entry->receipt->id . '</span>
                            </a>';
                } else {
                    return '<span class="badge bg-warning">No</span>';
                }
            })
            ->addColumn('loan', fn($entry) =>  number_format($entry->loanRepayments->sum('amount'),2))
            ->addColumn('action', fn($entry) => view('partials.actions', compact('entry'))->render())
            ->rawColumns(['receipt', 'action'])
            ->make(true);
    }
}