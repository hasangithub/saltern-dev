@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Payrolls')
@section('content_header_subtitle', 'Owner Loans')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
    <div class="container">
    <h2>Trial Balance</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Account Group</th>
                <th>Sub Group</th>
                <th>Ledger</th>
                <th class="text-end">Debit</th>
                <th class="text-end">Credit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trialData as $row)
                <tr>
                    <td>{{ $row['group'] }}</td>
                    <td>{{ $row['sub_group'] }}</td>
                    <td>{{ $row['ledger'] }}</td>
                    <td class="text-end">{{ number_format($row['debit'], 2) }}</td>
                    <td class="text-end">{{ number_format($row['credit'], 2) }}</td>
                </tr>
            @endforeach
            <tr class="fw-bold">
                <td colspan="3" class="text-end">Total</td>
                <td class="text-end">{{ number_format($totalDebit, 2) }}</td>
                <td class="text-end">{{ number_format($totalCredit, 2) }}</td>
            </tr>
        </tbody>
    </table>
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