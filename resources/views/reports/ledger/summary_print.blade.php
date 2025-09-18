@extends('layout.report_a4')
@section('section-title',  $ledger->name. ' Ledger')
@section('content')

<table>
    <thead>
        <tr class="table-secondary">
            <th style="width: 60px;">Date</th>
            <th>Description</th>
            <th class="text-right">Debit</th>
            <th class="text-right">Credit</th>
            <th class="text-right">Running Balance</th>
        </tr>
    </thead>
    <tbody>
        @php $running = $opening['balance']; @endphp

        <tr class="fw-bold bg-light">
            <td colspan="4">Opening Balance</td>
            <td class="text-right">
                {{ number_format(abs($running), 2) }}
                {{ $running >= 0 ? 'Dr' : 'Cr' }}
            </td>
        </tr>

        @foreach($journalDetails as $jd)
        @php $running += $jd->debit_amount - $jd->credit_amount; @endphp
        <tr>
            <td>{{ \Carbon\Carbon::parse($jd->journalEntry->journal_date)->format('Y-m-d') }}</td>
            <td>{{ $jd->description ?? $jd->journalEntry->description ?? '' }}</td>
            <td class="text-right">{{ number_format($jd->debit_amount, 2) }}</td>
            <td class="text-right">{{ number_format($jd->credit_amount, 2) }}</td>
            <td class="text-right">
                {{ number_format(abs($running), 2) }} {{ $running >= 0 ? 'Dr' : 'Cr' }}
            </td>
        </tr>
        @endforeach

        <tr class="table-secondary fw-bold">
            <td colspan="2">Total</td>
            <td class="text-right">{{ number_format($journalDetails->sum('debit_amount'), 2) }}</td>
            <td class="text-right">{{ number_format($journalDetails->sum('credit_amount'), 2) }}</td>
            <td></td>
        </tr>
    </tbody>
</table>
@endsection