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
                        <div class="col-md-4">
                            <strong>Name</strong>
                            <p class="text-muted">{{ $ownerLoan->membership->owner->full_name }}</p>
                            <hr>
                            <strong>Phone Number 1</strong>
                            <p class="text-muted">{{ $ownerLoan->membership->owner->phone_number }}</p>
                            <hr>
                            <strong>Phone Number 2</strong>
                            <p class="text-muted"> {{ $ownerLoan->membership->owner->secondary_phone_number }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Email</strong>
                            <p class="text-muted"> {{ $ownerLoan->membership->owner->email }}</p>
                            <hr>
                            <strong>Address 1</strong>
                            <p class="text-muted"> {{ $ownerLoan->membership->owner->address_line_1 }}</p>
                            <hr>
                            <strong>Address 2</strong>
                            <p class="text-muted"> {{ $ownerLoan->membership->owner->address_line_2 }}</p>
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
                    <h3 class="card-title">Loan Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Requested Amount</strong>
                            <p class="text-muted">{{ $ownerLoan->requested_amount }}</p>
                            <hr>
                            <strong>Approved Amount</strong>
                            <p class="text-muted"> {{ $ownerLoan->approved_amount }}</p>
                            <hr>
                            <strong>Status</strong><br>
                            <span class="badge {{ $ownerLoan->status === 'approved' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($ownerLoan->status) }}
                            </span>
                           
                            <hr>
                            @if ($ownerLoan->status === 'pending')
                            <button type="button" class="btn btn-warning" data-toggle="modal"
                                data-target="#approveLoanModal">
                                Approve Loan Request
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>

<div class="modal fade" id="approveLoanModal" tabindex="-1" aria-labelledby="approveLoanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('owner-loan.approve', $ownerLoan->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="approveLoanModalLabel">Approve Loan Request</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="approvedAmount" class="form-label">Approved Amount</label>
                        <input type="number" class="form-control" id="approvedAmount" name="approved_amount"
                            value="{{ $ownerLoan->approved_amount }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="comments" class="form-label">Comments</label>
                        <textarea class="form-control" id="approval_comments" name="approval_comments"
                            rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
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