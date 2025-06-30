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
                    <h3>Ledger Report - {{ $ledger->name }} → Sub-Ledger: {{ $subLedger->name }}</h3>
                    <p>From {{ $fromDate }} to {{ $toDate }}</p>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th class="text-right">Debit</th>
                                <th class="text-right">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalDebit = 0; $totalCredit = 0; @endphp
                            @forelse($journalDetails as $detail)
                            @php
                            $totalDebit += $detail->debit_amount;
                            $totalCredit += $detail->credit_amount;
                            @endphp
                            <tr>
                                <td>{{ $detail->journalEntry->journal_date ?? '' }}</td>
                                <td>{{ $detail->description }}</td>
                                <td class="text-right">{{ number_format($detail->debit_amount, 2) }}</td>
                                <td class="text-right">{{ number_format($detail->credit_amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No records found</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-right">Total</th>
                                <th class="text-right">{{ number_format($totalDebit, 2) }}</th>
                                <th class="text-right">{{ number_format($totalCredit, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
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