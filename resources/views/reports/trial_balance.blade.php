@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Reports')
@section('content_header_subtitle', 'Trial Balance')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Trial Balance</h3>
                    <a href="{{ route('trial-balance.print', request()->all()) }}" class="btn btn-primary"
                        target="_blank">
                        <i class="fas fa-print"></i> Print Trial Balance
                    </a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="meta">
                        @php
                        $printedOn = now()->format('Y-m-d H:i:s');
                        @endphp
                        <div>Printed on: <strong>{{ $printedOn }}</strong></div>
                        @if(request('from_date') && request('to_date'))
                        <div>Date Range: <strong>{{ request('from_date') }}</strong> to
                            <strong>{{ request('to_date') }}</strong></div>
                        @endif
                    </div>
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Sub Group</th>
                                <th>Ledger / Subledger</th>
                                <th class="text-right">Debit</th>
                                <th class="text-right">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trialData as $row)
                            <tr>

                                <td>{{ $row['sub_group'] }}</td>
                                <td>{!! $row['is_sub'] ? '<span class="ms-4 text-muted">' . $row['ledger'] . '</span>' :
                                    '<strong>' . $row['ledger'] . '</strong>' !!}</td>
                                <td class="text-right">{{ number_format($row['debit'], 2) }}</td>
                                <td class="text-right">{{ number_format($row['credit'], 2) }}</td>
                            </tr>
                            @endforeach
                            <tr class="font-weight-bold">
                                <td colspan="2" class="text-end">Total</td>
                                <td class="text-right">{{ number_format($totalDebit, 2) }}</td>
                                <td class="text-right">{{ number_format($totalCredit, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
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