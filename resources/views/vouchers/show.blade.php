@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Voucher')
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
                    <h3 class="card-title">Voucher Details</h3>
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
                            <p class="text-muted">{{ $voucher->name }}</p>
                            <hr>
                            <strong>Address</strong>
                            <p class="text-muted">{{ $voucher->address }}</p>
                            <hr>
                            <strong>Description</strong>
                            <p class="text-muted"> {{ $voucher->description }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Amount</strong>
                            <p class="text-muted"> {{ $voucher->amount }}</p>
                            <hr>
                            <strong>Note</strong>
                            <p class="text-muted"> {{ $voucher->note }}</p>
                            <hr>
                            <strong>Status</strong>
                            <p class="text-muted"> {{ $voucher->status }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Bank</strong>
                            <p class="text-muted"> {{ $voucher->amount }}</p>
                            <hr>
                            <strong>Cheque No</strong>
                            <p class="text-muted"> {{ $voucher->note }}</p>
                            <hr>
                            <strong>Cheque Date</strong>
                            <p class="text-muted"> {{ $voucher->status }}</p>
                        </div>
                    </div>
                    @if ($voucher->status === 'pending')
                            <button type="button" class="btn btn-warning" data-toggle="modal"
                                data-target="#approveLoanModal">
                                Approve Voucher
                            </button>
                            @endif
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>

<div class="modal fade" id="approveLoanModal" tabindex="-1" aria-labelledby="approveLoanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('voucher.approve', $voucher->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="approveLoanModalLabel">Approve Voucher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="comments" class="form-label">Comments</label>
                        <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
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