@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Owner Loans')
@section('content_header_subtitle', 'Owner Loans')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Owner Loans</h3>
                    <a href="{{ route('admin.owner_loans.create') }}" class="btn btn-success float-right"> <i
                            class="fas fa-plus"></i> Create Owner Loans </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table id="membershipsTable" class="table table-sm table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Requested Amount</th>
                                    <th>Approved Amount</th>
                                    <th>Outstanding</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ownerLoans as $ownerLoan)
                                <tr>
                                    <td>{{ $ownerLoan->formatted_date }}</td>
                                    <td>{{ $ownerLoan->membership->owner->name_with_initial }}</td>
                                    <td>{{ $ownerLoan->requested_amount }}</td>
                                    <td>{{ $ownerLoan->approved_amount }}</td>
                                    <td>{{ number_format($ownerLoan->approved_amount - $ownerLoan->ownerLoanRepayment->sum('amount') ?: 0, 2) }}</td>
                                    <td>{{ $ownerLoan->status }}</td>
                                    <td><a href="{{ route('owner-loans.show', $ownerLoan->id) }}"
                                            class="btn btn-default btn-xs">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="{{ route('loan-repayments.create-for-loan', $ownerLoan->id) }}"
                                            class="btn btn-primary btn-xs">
                                            + Repayment
                                        </a>
                                    </td>
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
    $('#membershipsTable').DataTable();
});
</script>
@endpush