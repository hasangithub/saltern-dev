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
                        <table id="membershipsTable" class="table table-sm table-bordered table-hover"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Loan ID</th>
                                    <th>Owner</th>
                                    <th>Buyer</th>
                                    <th>Repayment Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($repayments as $repayment)
                                <tr>
                                    <td>{{ $repayment->repayment_date  }}</td>
                                    <td>{{ $repayment->owner_loan_id  }}</td>
                                    <td>{{ $repayment->ownerLoan->membership->owner->name_with_initial  }}</td>
                                    <td>{{ $repayment->buyer->full_name  }}</td>   
                                    <td>{{ $repayment->amount  }}</td>
                                    <td>{{ $repayment->status  }}</td>    
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