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
                                    <th>Date</th>
                                    <th>Buyer</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($otherIncomes as $income)
                                <tr>
                                    <td>{{ $income->received_date }}</td>
                                    <td> {{ $income->buyer_id ? $income->buyer->business_name : 'Walkin buyer' }}</td>
                                    <td>{{ $income->incomeCategory->name }}</td>
                                    <td>{{ $income->amount }}</td>
                                    <td>{{ $income->name }}</td>
                                    <td>{{ $income->description }}</td>
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