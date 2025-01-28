@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'My Loan')
@section('content_header_subtitle', 'Welcome')
@section('plugins.Datatables', true)

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Owner Details Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Loan Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Date</strong>
                            <p class="text-muted">{{ $ownerLoan->created_at }}</p>
                            <hr>
                            <strong>Requested Amount</strong>
                            <p class="text-muted">{{ $ownerLoan->requested_amount }}</p>
                            <hr>
                            <strong>Approved Amount</strong>
                            <p class="text-muted"> {{ $ownerLoan->approved_amount }}</p>
                            <hr>
                            <strong>Status</strong><br>
                            <span
                                class="badge {{ $ownerLoan->status === 'approved' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($ownerLoan->status) }}
                            </span>
                        </div>
                        <div class="col-md-4">
                            <strong>Side</strong>
                            <p class="text-muted">{{ $ownerLoan->membership->saltern->yahai->side->name }}</p>
                            <hr>
                            <strong>Yahai</strong>
                            <p class="text-muted">{{ $ownerLoan->membership->saltern->yahai->name }}</p>
                            <hr>
                            <strong>Waikal</strong>
                            <p class="text-muted"> {{ $ownerLoan->membership->saltern->name }}</p>
                            <hr>
                            <strong></strong><br>
                            <span>
                              
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <!-- Owner Details Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Repayment History</h3>
                </div>
                <div class="card-body">
                    @if ($ownerLoan->ownerLoanRepayment->isEmpty())
                    <p>No repayments made yet.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ownerLoan->ownerLoanRepayment as $repayment)
                                <tr>
                                    <td>{{ $repayment->repayment_date }}</td>
                                    <td>{{ number_format($repayment->amount, 2) }}</td>
                                    <td>{{ $repayment->payment_method }}</td>
                                    <td>{{ $repayment->notes }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
            <!-- /.card -->
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
    $('#buyersTable').DataTable();
});
</script>
@endpush