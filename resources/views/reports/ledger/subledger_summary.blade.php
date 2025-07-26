@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Reports')
@section('content_header_subtitle', 'Production Report')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="container">
        <h4>Ledger Report with Subledgers: {{ $ledger->name }}</h4>
        <p>Period: {{ $fromDate }} to {{ $toDate }}</p>
        <div class="d-flex justify-content-end gap-2 mb-3">
            <form method="POST" action="{{ route('reports.ledger.pdf') }}" target="_blank" class="mr-2">
                @csrf
                <input type="hidden" name="from_date" value="{{ $fromDate }}">
                <input type="hidden" name="to_date" value="{{ $toDate }}">
                <input type="hidden" name="ledger_id" value="{{ $ledger->id }}">
                @if(isset($subLedger))
                <input type="hidden" name="sub_ledger_id" value="{{ $subLedger->id }}">
                @endif
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-print"></i> Print PDF
                </button>
            </form>

            <form method="GET" action="{{ route('ledger.report.export') }}" target="_blank">
                @csrf
                <input type="hidden" name="from_date" value="{{ $fromDate }}">
                <input type="hidden" name="to_date" value="{{ $toDate }}">
                <input type="hidden" name="ledger_id" value="{{ $ledger->id }}">
                @if(isset($subLedger))
                <input type="hidden" name="sub_ledger_id" value="{{ $subLedger->id }}">
                @endif
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-print"></i> Export
                </button>
            </form>
        </div>

        @foreach($subLedgerSummaries as $summary)
        @php
        $running = $summary['opening']['balance'];
        $details = $summary['journalDetails'];
        $subLedger = $summary['sub_ledger'];
        @endphp

        <h5 class="mt-4">{{ $subLedger->name }}</h5>
        <table class="table table-bordered table-sm">
            <thead>
                <tr class="table-secondary">
                    <th>Date</th>
                    <th>Description</th>
                    <th class="text-right">Debit</th>
                    <th class="text-right">Credit</th>
                    <th class="text-right">Running Balance</th>
                </tr>
            </thead>
            <tbody>
                <tr class="fw-bold bg-light">
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

                <tr class="table-secondary fw-bold">
                    <td colspan="2">Total</td>
                    <td class="text-right">{{ number_format($details->sum('debit_amount'), 2) }}</td>
                    <td class="text-right">{{ number_format($details->sum('credit_amount'), 2) }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        @endforeach
    </div>
</div>
@stop

{{-- Push extra CSS --}}

@push('css')
{{-- Add here extra stylesheets --}}
{{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@endpush

{{-- Push extra scripts --}}

@push('js')
<script>
$(document).ready(function() {
    $('#membershipsTable').DataTable();
});
</script>
@endpush