@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Other Income')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Other Incomes</h3>
                    <a href="{{ route('other_incomes.create') }}" class="btn btn-success ml-auto"> <i
                            class="fas fa-plus"></i> Create Other Income</a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table id="membershipsTable" class="table table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Buyer</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($otherIncomes as $income)
                                <tr>
                                    <td>{{ $income->id }}</td>
                                    <td>{{ $income->received_date }}</td>
                                    <td> {{ $income->buyer_id ? $income->buyer->full_name : 'Walkin buyer' }}</td>
                                    <td>{{ $income->incomeCategory->name }}</td>
                                    <td>{{ $income->amount }}</td>
                                    <td>{{ $income->description }}</td>
                                    <td> @if($income->receipt)
                                        <a href="{{ route('receipts.show', $income->receipt->id) }}" target="_blank">
                                            <span class="badge bg-success">Paid (Receipt
                                                #{{ $income->receipt->id }})</span>
                                        </a>
                                        @else
                                        <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                    <a href="{{ route('other-income.print', $income->id) }}" class="btn btn-primary"
                                        target="_blank">
                                        <i class="fas fa-print"></i> Print
                                    </a>
                                    <a href="{{ route('other-income.a4print', $income->id) }}" class="btn btn-primary"
                                        target="_blank">
                                        <i class="fas fa-print"></i> PrintA4
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
    $('#membershipsTable').DataTable({
        order: [
            [0, 'desc']
        ],
        pageLength: 50
    });
});
</script>
@endpush