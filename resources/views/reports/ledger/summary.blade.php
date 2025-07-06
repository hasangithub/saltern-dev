@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Reports')
@section('content_header_subtitle', 'Production Report')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Ledger Report - {{ $ledger->name }}</h3>
                    <p>From {{ $fromDate }} to {{ $toDate }}</p>
                </div>
                <div class="card-body">
                    @if(isset($subLedgerSummaries))
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sub-Ledger</th>
                                <th>Debit</th>
                                <th>Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subLedgerSummaries as $summary)
                            <tr>
                                <td>{{ $summary['sub_ledger']->name }}</td>
                                <td>{{ number_format($summary['debit'], 2) }}</td>
                                <td>{{ number_format($summary['credit'], 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    {{-- Ledger has no subledgers --}}
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Debit</th>
                                <th>Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $totalDebit = 0;
                            $totalCredit = 0;
                            @endphp
                            @forelse($journalDetails as $entry)
                            <tr>
                                <td>{{ $entry->journalEntry->journal_date }}</td>
                                <td>{{ $entry->description }}</td>
                                <td class="text-end">{{ number_format($entry->debit_amount, 2) }}</td>
                                <td class="text-end">{{ number_format($entry->credit_amount, 2) }}</td>
                            </tr>
                            @php
                            $totalDebit += $entry->debit_amount;
                            $totalCredit += $entry->credit_amount;
                            @endphp
                            @empty
                            <tr>
                                <td colspan="4">No transactions found.</td>
                            </tr>
                            @endforelse
                            <tr>
                                <th colspan="2" class="text-end">Total</th>
                                <th class="text-end">{{ number_format($totalDebit, 2) }}</th>
                                <th class="text-end">{{ number_format($totalCredit, 2) }}</th>
                            </tr>
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
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