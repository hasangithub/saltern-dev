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
use Barryvdh\DomPDF\Facade\Pdf;

class OtherIncomeController extends Controller
{
    public function index()
    {
        $otherIncomes = OtherIncome::with('incomeCategory', 'buyer', 'receipt')->get();
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
        $validated = $request->validate([
            'buyer_id' => 'required|exists:buyers,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['received_date'] = date("Y-m-d"); 
        $validated['income_category_id'] = $request->input('ledger_id'); 
      
        $otherIncome = OtherIncome::create($validated);

        // Categories: 162, 163, 165 → ledger_id = 10, else ledger_id = 27
        switch ($request->input('ledger_id')) {
            case 162:
                $ledgerId = 10;
                $subLedgerId = 97;
                break;
            case 163:
                $ledgerId = 10;
                $subLedgerId = 98;
                break;
            case 165:
                $ledgerId = 10;
                $subLedgerId = 99;
                break;
            default:
                $ledgerId = 10;
                $subLedgerId = null;
                break;
        }

        $journal = JournalEntry::create([
            'journal_date' => Carbon::now()->toDateString(), // YYYY-MM-DD
            'description' => 'Other Income#'.$otherIncome->id,
        ]);

        $details = [
            [
                'journal_id' => $journal->id,
                'ledger_id' => $ledgerId,
                'sub_ledger_id' => $subLedgerId,
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

    public function printOtherIncome(OtherIncome $income)
    {
        $pdf = Pdf::loadView('other_incomes.print', [
            'income' => $income,
            'from_pdf' => true,
        ])
        ->setPaper('A6', 'portrait')
        ->setOptions([
            'defaultFont' => 'Times-Roman',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true,
            'isFontSubsettingEnabled' => true
        ]);
    
        return $pdf->stream("other_income_{$income->id}.pdf");
    }
}
