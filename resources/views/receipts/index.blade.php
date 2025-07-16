@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Receipts')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Receipts</h3>
                    <a href="{{ route('receipts.create') }}" class="btn btn-success ml-auto"> <i class="fas fa-plus"></i>
                        Create Receipts</a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Buyer</th>
                                    <th>Total Amount</th>
                                    <th>Bank</th>
                                    <th>Created By</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($receipts as $receipt)
                                <tr>
                                    <td>{{ $receipt->id }}</td>
                                    <td>{{ $receipt->receipt_date }}</td>
                                    <td>{{ $receipt->buyer?->full_name ?? '-' }}</td>
                                    <td>{{ number_format($receipt->total_amount, 2) }}</td>
                                    <td> @if ($receipt->bank_sub_ledger_id)
                                        {{ $receipt->bank->name }} / {{ $receipt->cheque_no }} / {{ $receipt->cheque_date }}
                                        @else

                                        @endif
                                    </td>
                                    <td>{{ $receipt->createdBy?->name ?? '-' }}</td>
                                    <td>
                                    <a href="{{ route('receipts.show', $receipt->id) }}" class="btn btn-sm btn-info">View</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6">No receipts found.</td>
                                </tr>
                                @endforelse
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
    $('#ownersTable').DataTable();
});
</script>
@endpush