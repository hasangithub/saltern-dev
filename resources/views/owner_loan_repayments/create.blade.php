@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Owner Loan')
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
                    <h3 class="card-title">Owner Details</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Date</strong>
                            <p class="text-muted">{{ $ownerLoan->formatted_date }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Requested Amount</strong>
                            <p class="text-muted">{{ number_format($ownerLoan->requested_amount, 2) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Approved Amount</strong>
                            <p class="text-muted">{{ number_format($ownerLoan->approved_amount, 2) }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Status</strong><br>
                            <span
                                class="badge {{ $ownerLoan->status === 'approved' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($ownerLoan->status) }}
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
            <!-- Loan Repayment History -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5>Repayment History</h5>
                    @if ($ownerLoan->ownerLoanRepayment->isEmpty())
                    <p>No repayments made yet.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Buyer</th>
                                    <th>Notes</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ownerLoan->ownerLoanRepayment as $repayment)
                                <tr>
                                    <td>{{ $repayment->repayment_date }}</td>
                                    <td>{{ number_format($repayment->amount, 2) }}</td>
                                    <td>{{ $repayment->buyer->full_name }}</td>
                                    <td>{{ $repayment->notes }}</td>
                                    <td>{{ $repayment->status }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @if ($outstandingBalance > 0)
    <button type="button" class="btn btn-primary mb-2" data-toggle="modal" data-target="#repaymentModal">
        Add Repayment
    </button>
    @else
    <p class="text-success">No outstanding balance.</p>
    @endif
</div>

<!-- Repayment Modal -->
<div class="modal fade" id="repaymentModal" tabindex="-1" aria-labelledby="repaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('loan-repayments.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="repaymentModalLabel">Add Repayment</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="owner_loan_id" value="{{ $ownerLoan->id }}">
                    <div class="form-group mb-3">
                        <label for="buyer_id">Buyer</label>
                        <select name="buyer_id" id="buyer_id" class="form-control" required>
                            <option value="">Select Buyer</option>
                            @foreach($buyers as $buyer)
                            <option value="{{ $buyer->id }}">{{ $buyer->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="repayment_amount">Amount</label>
                        <input type="number" name="amount" id="repayment_amount" class="form-control" step="0.01"
                            required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="repayment_date">Repayment Date</label>
                        <input type="date" name="repayment_date" id="repayment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="notes">Notes</label>
                        <textarea name="notes" id="notes" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Repayment</button>
                </div>
            </form>
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