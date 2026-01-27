<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\JournalDetail;
use App\Models\JournalEntry;
use App\Models\Ledger;
use App\Models\Membership;
use App\Models\OtherIncome;
use App\Models\Owner;
use App\Models\PrivateWeighbridgeEntry;
use App\Models\Receipt;
use App\Models\ReceiptDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PrivateWeighbridgeEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $entries = PrivateWeighbridgeEntry::with(['buyer'])
            ->get();

        return view('private_weighbridge_entries.index', compact('entries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $buyers = Buyer::all();
        $nextSerialNo = PrivateWeighbridgeEntry::max('id') + 1;

        return view('private_weighbridge_entries.create', compact('buyers','nextSerialNo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id'        => 'required',
            'transaction_date'  => 'required|date',
            'first_weight'      => 'required|numeric|min:0',
            'customer_name'     => 'nullable|string|max:255',
            'buyer_id'          => 'nullable|exists:buyers,id',
        ]);

        $data['first_weight_time'] = now()->format('H:i:s');
        $data['created_by'] =  auth('web')->id(); 
        $data['status'] = 'pending';

        PrivateWeighbridgeEntry::create($data);

        return redirect()
            ->route('private-weighbridge-entries.index')
            ->with('success', 'First weight recorded successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PrivateWeighbridgeEntry $privateWeighbridgeEntry)
    {
        $buyers = Buyer::all();

        return view('private_weighbridge_entries.edit', compact('privateWeighbridgeEntry', 'buyers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PrivateWeighbridgeEntry $privateWeighbridgeEntry)
    {
        if ($privateWeighbridgeEntry->second_weight !== null) {
            return back()->withErrors('Second weight already recorded');
        }

        $data = $request->validate([
            'second_weight' => 'nullable|numeric|min:0',
            'amount'        => 'required|numeric|min:0',
            'buyer_id'      => 'nullable|exists:buyers,id',
            'is_paid'       => 'nullable|boolean',
        ]);

        $data['is_paid'] = $request->has('is_paid') ? 1 : 0;
        $data['second_weight_time'] = now()->format('H:i:s');
        $data['updated_by'] =  auth('web')->id(); 
        $data['status'] = 'completed';

        $privateWeighbridgeEntry->update($data);

        $incomeData['buyer_id'] = $privateWeighbridgeEntry->buyer_id;
        $incomeData['amount'] = $privateWeighbridgeEntry->amount;
        $incomeData['description'] = ''; 
        $incomeData['income_category_id'] = 165; 
        $incomeData['received_date'] = date("Y-m-d"); 

        if ($data['is_paid']) {
            $incomeData['status'] = 'paid'; 
            $otherIncome = OtherIncome::create($incomeData);

            $privateWeighbridgeEntry->update(['other_income_id' => $otherIncome->id]);

            $journal = JournalEntry::create([
                'journal_date' => Carbon::now()->toDateString(), // YYYY-MM-DD
                'description' => 'Private Weighbridge#'.$privateWeighbridgeEntry->id,
            ]);
    
            $details = [
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => 10,
                    'sub_ledger_id' => 99,
                    'debit_amount' => $privateWeighbridgeEntry->amount,
                    'credit_amount' => null,
                    'description' => '',
                ],
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => 165,
                    'sub_ledger_id' => null,
                    'debit_amount' => null,
                    'credit_amount' => $privateWeighbridgeEntry->amount,
                    'description' => '',
                ],
            ];
    
            JournalDetail::insert($details);

            $receiptData['buyer_id'] = $privateWeighbridgeEntry->buyer_id;
            $receiptData['payment_method_id'] = 2;
            $receiptData['bank_sub_ledger_id'] = null;
            $receiptData['cheque_no'] = null;
            $receiptData['cheque_date'] = null;
            $receiptData['receipt_date'] = now();
            $receiptData['total_amount'] = $privateWeighbridgeEntry->amount;
            $receiptData['created_by'] = auth('web')->id();

            $receipt = Receipt::create($receiptData);

            ReceiptDetail::create([
                'receipt_id' => $receipt->id,
                'entry_type' => 'other_income',
                'entry_id'   => $otherIncome->id,
                'amount'     => $privateWeighbridgeEntry->amount
            ]);

            $journal = JournalEntry::create([
                'journal_date' => Carbon::now()->toDateString(), // YYYY-MM-DD
                'description' => 'Receipt Other Income Receipt#'.$receipt->id,
            ]);

            $details = [
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => 10,
                    'sub_ledger_id' => 99,
                    'debit_amount' => null,
                    'credit_amount' => $privateWeighbridgeEntry->amount,
                    'description' => '',
                ],
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => 11,
                    'sub_ledger_id' => 103,
                    'debit_amount' => $privateWeighbridgeEntry->amount,
                    'credit_amount' => null,
                    'description' => '',
                ],
            ];
    
            JournalDetail::insert($details);

        } else {
           
            $otherIncome = OtherIncome::create($incomeData);
            $privateWeighbridgeEntry->update(['other_income_id' => $otherIncome->id]);

            $journal = JournalEntry::create([
                'journal_date' => Carbon::now()->toDateString(), // YYYY-MM-DD
                'description' => 'Private Weighbridge#'.$privateWeighbridgeEntry->id,
            ]);
    
            $details = [
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => 10,
                    'sub_ledger_id' => 99,
                    'debit_amount' => $privateWeighbridgeEntry->amount,
                    'credit_amount' => null,
                    'description' => '',
                ],
                [
                    'journal_id' => $journal->id,
                    'ledger_id' => 165,
                    'sub_ledger_id' => null,
                    'debit_amount' => null,
                    'credit_amount' => $privateWeighbridgeEntry->amount,
                    'description' => '',
                ],
            ];
    
            JournalDetail::insert($details);
        }

        return redirect()
            ->route('private-weighbridge-entries.index')
            ->with('success', 'Entry created successfully. Printing invoice...')
            ->with('print_type', 'second')
            ->with('print_entry_id', $privateWeighbridgeEntry->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function invoice(PrivateWeighbridgeEntry $entry, Request $request)
    {
        $mode = $request->query('mode');
       
        $pdf = Pdf::loadView('private_weighbridge_entries.invoice', ['entry' => $entry, 'from_pdf' => true, 'mode'=> $mode])
        ->setPaper('A6', 'portrait')
        ->setOptions([
            'defaultFont' => 'times',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true,
            'isFontSubsettingEnabled' => true
        ]);// A6 in points
        return $pdf->stream("invoice_{$entry->id}.pdf");

        //return view('weighbridge_entries.invoice', compact('entry'));
    }
}
