@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Owner Loan Repayments')
@section('content_header_subtitle', 'Owner Loan Repayments')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Owner Loan Repayments</h3>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table id="membershipsTable" class="table table-sm nowrap table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Repayment ID</th>
                                    <th>Date</th>
                                    <th>Loan ID</th>
                                    <th>Owner</th>
                                    <th>Yahai</th>
                                    <th>Waikal</th>
                                    <th>Buyer</th>
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
                                    <td>{{ $repayment->owner_loan_id  }}</td>
                                    <td>{{ $repayment->ownerLoan->membership->owner->name_with_initial  }}</td>
                                    <td>{{ $repayment->ownerLoan->membership->saltern->yahai->name  }}</td>
                                    <td>{{ $repayment->ownerLoan->membership->saltern->name  }}</td>
                                    <td>{{ $repayment->buyer->full_name  }}</td>
                                    <td>{{ $repayment->amount  }}</td>
                                    <td> @if($repayment->receipt)
                                        <a href="{{ route('receipts.show', $repayment->receipt->id) }}" target="_blank">
                                            <span class="badge bg-success">Paid (Receipt
                                                #{{ $repayment->receipt->id }})</span>
                                        </a>
                                        @else
                                        <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td><a href="{{ route('loan-repayment.print', $repayment->id) }}"
                                            class="btn btn-sm btn-primary" target="_blank">
                                            <i class="fas fa-print"></i> Print
                                        </a>
                                        <form action="{{ route('owner-loan-repayments.destroy', $repayment->id) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this repayment#{{ $repayment->id }}?')">
                                                Delete
                                            </button>
                                        </form>
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
    $('#membershipsTable').DataTable({
        order: [
            [0, 'desc']
        ],
        pageLength: 50
    });
});
</script>
@endpush