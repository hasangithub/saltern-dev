<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;


class LedgerReportExport implements FromArray, WithHeadings, WithCustomStartCell, WithEvents
{
    protected $type; // 'subledger_detail', 'subledger_summary', or 'ledger_detail'
    protected $data;
    protected $salternName;
    protected $reportTitle;
    protected $period;

    public function __construct($type, $data, $salternName = '', $reportTitle = '', $period = '')
    {
        $this->type = $type;
        $this->data = $data;
        $this->salternName = $salternName;
        $this->reportTitle = $reportTitle;
        $this->period = $period;
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
                return ['Date', 'Description', 'Debit', 'Credit', 'Balance'];
            case 'ledger_detail':
                return ['Date', 'Description', 'Debit', 'Credit', 'Balance'];
            default:
                return [];
        }
    }

    public function registerEvents(): array
{
    return [
        BeforeSheet::class => function (BeforeSheet $event) {
            $sheet = $event->sheet->getDelegate();

            // Custom header rows
            $sheet->insertNewRowBefore(1, 4); // Push everything down by 4 rows
            $sheet->setCellValue('A1', $this->salternName);
            $sheet->setCellValue('A2', $this->reportTitle);
            $sheet->setCellValue('A3', $this->period);

            // Optional: Merge cells across all columns (assuming 5 columns)
            $sheet->mergeCells('A1:E1');
            $sheet->mergeCells('A2:E2');
            $sheet->mergeCells('A3:E3');

            // Optional: Style
            $sheet->getStyle('A1:A3')->getFont()->setBold(true);
            $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        },
    ];
}


    public function startCell(): string
    {
        return 'A4';
    }

    protected function prepareSubledgerDetail($data)
    {
        // $data contains: ['opening' => ..., 'journalDetails' => ...]
        $rows = [];

        // Opening balance row
        $running = $data['opening']['balance'];
        $rows[] = ['Opening Balance', '', '', '', round((float) $running, 2)];
       
       
        foreach ($data['journalDetails'] as $entry) {
            $running += $entry->debit_amount - $entry->credit_amount;
            $rows[] = [
                $entry->journalEntry->journal_date,
                $entry->journalEntry->description,
                round($entry->debit_amount, 2),
                round($entry->credit_amount, 2),
                round($running, 2),
            ];
        }
        return $rows;
    }

    protected function prepareSubledgerSummary($data)
    {
        // $data contains: ['subLedgerSummaries' => [...]]
        $rows = [];
      
        foreach ($data['subLedgerSummaries'] as $summary) {
            $running = 0.00;
            $running = $summary['opening']['balance'];
            $rows[] = [$summary['sub_ledger']->name, '', '', '', ''];
            $rows[] = ['Opening Balance', '', '', '', round((float) $running, 2)];
                    
            $journalDetails = $summary['journalDetails'];
 
            foreach($journalDetails as $detail) {
                $running += $detail->debit_amount - $detail->credit_amount;
                $rows[] = [
                    $detail->journalEntry->journal_date,
                    $detail->journalEntry->description,
                    round($detail->debit_amount, 2),
                    round($detail->credit_amount, 2),
                    round($running, 2),
                ];
            }
            $rows[] = ['', '', '', '', ''];
            $rows[] = ['', '', '', '', ''];
        }
        return $rows;
    }

    protected function prepareLedgerDetail($data)
    {
        // Similar to subledger detail but without subledger filtering
        $rows = [];
        $running = $data['opening']['balance'];
        $rows[] = ['Opening Balance', '', '', '', round((float) $running, 2)];
       
        foreach ($data['journalDetails'] as $entry) {
            $running += $entry->debit_amount - $entry->credit_amount;
            $rows[] = [
                $entry->journalEntry->journal_date,
                $entry->journalEntry->description,
                round($entry->debit_amount, 2),
                round($entry->credit_amount, 2),
                round($running, 2),
            ];
        }
        return $rows;
    }
}
