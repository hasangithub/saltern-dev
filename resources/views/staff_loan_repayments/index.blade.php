@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Staff Loan Repayments')
@section('content_header_subtitle', 'Staff Loan Repayments')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table id="membershipsTable" class="table table-sm table-bordered table-hover"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>Repayment ID</th>
                                    <th>Date</th>
                                    <th>Staff Name</th>
                                    <th>Loan ID</th>
                                    <th>Repayment Amount</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($repayments as $repayment)
                                <tr>
                                    <td>{{ $repayment->id  }}</td>
                                    <td>{{ $repayment->repayment_date  }}</td>
                                    <td>{{ $repayment->staffLoan->user->name  }}</td>
                                    <td>{{ $repayment->staff_loan_id  }}</td>
                                    <td>{{ $repayment->amount  }}</td>
                                    <td>{{ $repayment->status  }}</td>
                                    <td></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
    $('#membershipsTable').DataTable({
        order: [
            [0, 'desc']
        ],
        pageLength: 50
    });
});
</script>
@endpush