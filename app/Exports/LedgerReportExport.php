<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LedgerReportExport implements FromArray, WithHeadings
{
    protected $type; // 'subledger_detail', 'subledger_summary', or 'ledger_detail'
    protected $data;

    public function __construct($type, $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    public function array(): array
    {
        // Return array of rows depending on type

        switch ($this->type) {
            case 'subledger_detail':
                return $this->prepareSubledgerDetail($this->data);
            case 'subledger_summary':
                return $this->prepareSubledgerSummary($this->data);
            case 'ledger_detail':
                return $this->prepareLedgerDetail($this->data);
            default:
                return [];
        }
    }

    public function headings(): array
    {
        switch ($this->type) {
            case 'subledger_detail':
                return ['Date', 'Description', 'Debit', 'Credit', 'Balance'];
            case 'subledger_summary':
                return ['Subledger Name', 'Opening Balance', 'Debit', 'Credit', 'Closing Balance'];
            case 'ledger_detail':
                return ['Date', 'Description', 'Debit', 'Credit', 'Balance'];
            default:
                return [];
        }
    }

    protected function prepareSubledgerDetail($data)
    {
        // $data contains: ['opening' => ..., 'journalDetails' => ...]
        $rows = [];

        // Opening balance row
        $rows[] = ['Opening Balance', '', '', '', $data['opening']['balance']];

        foreach ($data['journalDetails'] as $entry) {
            $rows[] = [
                $entry->journal_date,
                $entry->description,
                $entry->debit_amount,
                $entry->credit_amount,
                $entry->balance,
            ];
        }
        return $rows;
    }

    protected function prepareSubledgerSummary($data)
    {
        // $data contains: ['subLedgerSummaries' => [...]]
        $rows = [];

        foreach ($data['subLedgerSummaries'] as $summary) {
            $sub = $summary['sub_ledger'];
            $opening = $summary['opening'];
            $journalDetails = $summary['journalDetails'];

            // Calculate totals for this subledger
            $totalDebit = $journalDetails->sum('debit_amount');
            $totalCredit = $journalDetails->sum('credit_amount');
            $closingBalance = $opening['balance'] + $totalDebit - $totalCredit;

            $rows[] = [
                $sub->name,
                $opening['balance'],
                $totalDebit,
                $totalCredit,
                $closingBalance,
            ];
        }
        return $rows;
    }

    protected function prepareLedgerDetail($data)
    {
        // Similar to subledger detail but without subledger filtering
        $rows = [];

        $rows[] = ['Opening Balance', '', '', '', $data['opening']['balance']];

        foreach ($data['journalDetails'] as $entry) {
            $rows[] = [
                $entry->journalEntry->journal_date,
                $entry->journalEntry->description,
                $entry->debit_amount,
                $entry->credit_amount,
                $entry->balance,
            ];
        }
        return $rows;
    }
}
