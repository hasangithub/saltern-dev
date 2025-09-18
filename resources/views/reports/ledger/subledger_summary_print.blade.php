@extends('layout.report_a4')
@section('section-title',  $ledger->name. ' Ledger')
@section('content')

    @foreach($subLedgerSummaries as $summary)
        @php
            $running = $summary['opening']['balance'];
            $details = $summary['journalDetails'];
            $subLedger = $summary['sub_ledger'];
        @endphp

        <h5>{{ $subLedger->name }}</h5>
        <table>
            <thead>
                <tr style="background-color: #ddd;">
                    <th style="width: 60px;">Date</th>
                    <th>Description</th>
                    <th class="text-right">Debit</th>
                    <th class="text-right">Credit</th>
                    <th class="text-right">Running Balance</th>
                </tr>
            </thead>
            <tbody>
                <tr class="bg-light fw-bold">
                    <td colspan="4">Opening Balance</td>
                    <td class="text-right">
                        {{ number_format(abs($running), 2) }} {{ $running >= 0 ? 'Dr' : 'Cr' }}
                    </td>
                </tr>

                @foreach($details as $jd)
                    @php $running += $jd->debit_amount - $jd->credit_amount; @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($jd->journalEntry->journal_date)->format('Y-m-d') }}</td>
                        <td>{{ $jd->journalEntry->description }}</td>
                        <td class="text-right">{{ number_format($jd->debit_amount, 2) }}</td>
                        <td class="text-right">{{ number_format($jd->credit_amount, 2) }}</td>
                        <td class="text-right">
                            {{ number_format(abs($running), 2) }} {{ $running >= 0 ? 'Dr' : 'Cr' }}
                        </td>
                    </tr>
                @endforeach

                <tr class="fw-bold">
                    <td colspan="2">Total</td>
                    <td class="text-right">{{ number_format($details->sum('debit_amount'), 2) }}</td>
                    <td class="text-right">{{ number_format($details->sum('credit_amount'), 2) }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    @endforeach
@endsection