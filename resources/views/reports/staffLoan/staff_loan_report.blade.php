@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Reports')
@section('content_header_subtitle', 'Report')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <p class=""><strong>Staff:</strong> </p>
            <a href="{{ route('staff-loan.print', request()->all()) }}" class="btn btn-primary"
                        target="_blank">
                        <i class="fas fa-print"></i> Print
                    </a>
        </div>

        @foreach ($grouped as $saltern => $loans)
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0 text-primary">{{ $saltern }}</h5>
                </div>
                <div class="card-body p-3">

                    @foreach ($loans as $loan)
                    <div class="mb-4">
                        <table class="table table-bordered table-sm mb-2">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th class="text-right">Debit</th>
                                    <th class="text-right">Credit</th>
                                    <th class="text-right">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $totalDebit = 0;
                                $totalCredit = 0;
                                $finalBalance = 0;
                                @endphp

                                @foreach ($loan['rows'] as $row)
                                @php
                                $totalDebit += $row['debit'] ?? 0;
                                $totalCredit += $row['credit'] ?? 0;
                                $finalBalance = $row['balance'];
                                @endphp
                                <td>{{ $row['date'] }}</td>
                                <td>{{ $row['description'] }}</td>
                                <td class="text-right">{{ $row['debit'] ? number_format($row['debit'], 2) : '' }}</td>
                                <td class="text-right">{{ $row['credit'] ? number_format($row['credit'], 2) : '' }}
                                </td>
                                <td class="text-right fw-bold">{{ number_format($row['balance'], 2) }}</td>
                                </tr>
                                @endforeach
                                <tr class="fw-bold table-light">
                                    <td colspan="2" class="text-right">Total</td>
                                    <td class="text-right">{{ number_format($totalDebit, 2) }}</td>
                                    <td class="text-right">{{ number_format($totalCredit, 2) }}</td>
                                    <td class="text-right">{{ number_format($finalBalance, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>
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