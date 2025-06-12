@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'My Loans')
@section('content_header_subtitle', 'My  Loans')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Loans</h3>
                    <a href="{{ route('staff-loans.create') }}" class="btn btn-success ml-auto"> <i
                            class="fas fa-plus"></i> Request New Loan</a>
                </div>

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
                                @foreach($staffLoans as $loan)
                                <tr>
                                    <td>{{ $loan->formatted_date  }}</td>
                                    <td>{{ $loan->user->name }}</td>
                                    <td>{{ $loan->requested_amount }}</td>
                                    <td>{{ $loan->approved_amount }}</td>
                                    <td>{{ number_format($loan->approved_amount - $loan->staffLoanRepayment->sum('amount') ?: 0, 2) }}</td>
                                    <td>@if($loan->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                        @else
                                        <span class="badge bg-secondary">{{ ucfirst($loan->status) }}</span>
                                        @endif
                                    </td>
                                    <td><a href="{{ route('staff.my-loans.show', $loan->id) }}"
                                            class="btn btn-default btn-xs">
                                            <i class="fas fa-eye"></i> View
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