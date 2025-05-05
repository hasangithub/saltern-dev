@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Payrolls')
@section('content_header_subtitle', 'Owner Loans')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Payrolls</h3>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <h2>Payroll for {{ $payrolls->first()->month ?? '' }}/{{ $payrolls->first()->year ?? '' }}</h2>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Basic Salary</th>
                                <th>Deductions</th>
                                <th>Net Salary</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payrolls as $pay)
                            <tr>
                                <td>{{ $pay->user->name }}</td>
                                <td>{{ number_format($pay->basic_salary, 2) }}</td>
                                <td>{{ number_format($pay->deductions, 2) }}</td>
                                <td>{{ number_format($pay->net_salary, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
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