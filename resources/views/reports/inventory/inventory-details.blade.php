@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Reports')
@section('content_header_subtitle', 'Trial Balance')
@section('page-buttons')
<a href="{{ route('inventories.report.print', request()->all()) }}" class="btn btn-primary" target="_blank">
    <i class="fas fa-print"></i> Print
</a>
@endsection
{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="callout callout-info">
                {{ now()->format('Y-m-d H:i:s') }}<br>
                @if(request('from_date') && request('to_date'))
                {{ request('from_date') }} -
                {{ request('to_date') }}
                @endif
            </div>
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-danger">Total Amount: {{ number_format($vouchers->sum('amount'), 2) }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Stock Code</th>
                                <th>Qty</th>
                                <th>Place</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vouchers as $voucher)
                            <tr>
                                <td>{{ $voucher->created_at->format('Y-m-d') }}</td>
                                <td>{{ $voucher->name }}</td>
                                <td>{{ $voucher->stock_code }}</td>
                                <td>{{ $voucher->qty }}</td>
                                <td>{{ $voucher->place->name}}
                                </td>
                                <td class="text-right">{{ number_format($voucher->amount, 2) }}</td>
                                <td>{{ ucfirst($voucher->status) }}</td>
                            </tr>
                            @endforeach
                            <tr class="fw-bold">
                                <td colspan="5" class="text-right">Total</td>
                                <td class="text-right">
                                    {{ number_format($vouchers->sum('amount'), 2) }}
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
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