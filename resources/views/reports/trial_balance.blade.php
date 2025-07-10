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
                <div class="card-header">
                    <h3 class="card-title">Trial Balance</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Account Group</th>
                                <th>Sub Group</th>
                                <th>Ledger / Subledger</th>
                                <th class="text-end">Debit</th>
                                <th class="text-end">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trialData as $row)
                            <tr>
                                <td>{{ $row['group'] }}</td>
                                <td>{{ $row['sub_group'] }}</td>
                                <td>
                                    {!! $row['is_sub'] ? '<span class="ms-3 text-muted">' . $row['ledger'] . '</span>' :
                                    '<strong>' . $row['ledger'] . '</strong>' !!}
                                </td>
                                <td class="text-end">{{ number_format($row['debit'], 2) }}</td>
                                <td class="text-end">{{ number_format($row['credit'], 2) }}</td>
                            </tr>
                            @endforeach
                            <tr class="fw-bold text-primary">
                                <td colspan="3" class="text-end">Total</td>
                                <td class="text-end">{{ number_format($totalDebit, 2) }}</td>
                                <td class="text-end">{{ number_format($totalCredit, 2) }}</td>
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