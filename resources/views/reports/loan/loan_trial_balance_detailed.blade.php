@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Reports')
@section('content_header_subtitle', 'Production Buyer Report')

{{-- Content body: main page content --}}

@section('content_body')
<style>
@media print {
    body * {
        visibility: hidden;
    }

    #printable-area,
    #printable-area * {
        visibility: visible;
    }

    #printable-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    button {
        display: none;
    }
}
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"></h3>
                    <a href="{{ route('loan-trial-balance.print', request()->all()) }}" class="btn btn-primary"
                        target="_blank">
                        <i class="fas fa-print"></i> Print Loan Trial Balance
                    </a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    @foreach ($grouped as $yahai => $records)
                    <h5 class="mt-4 fw-bold">{{ $yahai }}</h5>
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr class="table-secondary">
                                <th>Saltern</th>
                                <th>Owner</th>
                                <th>Loan ID</th>
                                <th class="text-right">Approved</th>
                                <th class="text-right">Repaid</th>
                                <th class="text-right">Outstanding</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($records as $row)
                            <tr>
                                <td>{{ $row['saltern'] }}</td>
                                <td>{{ $row['owner'] }}</td>
                                <td>{{ $row['loan_id'] }}</td>
                                <td class="text-right">{{ number_format($row['approved'], 2) }}</td>
                                <td class="text-right">{{ number_format($row['repaid'], 2) }}</td>
                                <td class="text-right">{{ number_format($row['outstanding'], 2) }}</td>
                            </tr>
                            @endforeach
                            <tr class="table-secondary fw-bold">
                                <td colspan="5" class="text-right">Total Outstanding ({{ $yahai }})</td>
                                <td class="text-right">{{ number_format($yahaiTotals[$yahai], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    @endforeach

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