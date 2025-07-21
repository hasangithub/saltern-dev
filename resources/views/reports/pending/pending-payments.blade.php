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
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Buyer Name</th>
                        <th>Weighbridge (Unpaid)</th>
                        <th>Loan Repayments (Pending)</th>
                        <th>Other Income (Pending)</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report as $row)
                    <tr>
                        <td>{{ $row['buyer']->full_name ?? $row['buyer']->name }}</td>
                        <td class="text-right"> {{ number_format($row['weighbridge'], 2) }}</td>
                        <td class="text-right"> {{ number_format($row['loan'], 2) }}</td>
                        <td class="text-right"> {{ number_format($row['income'], 2) }}</td>
                        <td class="text-right fw-bold"> {{ number_format($row['total'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-info">
                    <tr>
                        <th>Total</th>
                        <th class="text-right"> {{ number_format($grandTotal['weighbridge'], 2) }}</th>
                        <th class="text-right"> {{ number_format($grandTotal['loan'], 2) }}</th>
                        <th class="text-right"> {{ number_format($grandTotal['income'], 2) }}</th>
                        <th class="text-right fw-bold"> {{ number_format($grandTotal['total'], 2) }}</th>
                    </tr>
                </tfoot>
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