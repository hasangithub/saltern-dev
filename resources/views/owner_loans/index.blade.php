@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'My Loans')
@section('content_header_subtitle', 'My Loans')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('owner.my-loans.index') }}">
                        <div class="row">
                            <!-- Saltern Dropdown -->
                            <div class="col-md-4">
                                <label for="saltern_id" class="form-label">Filter by Saltern</label>
                                <select name="saltern_id" id="saltern_id" class="form-control">
                                    <option value="">All Salterns</option>
                                    @foreach ($salterns as $saltern)
                                    <option value="{{ $saltern->id }}"
                                        {{ request('saltern_id') == $saltern->id ? 'selected' : '' }}>
                                        {{ $saltern->saltern->yahai->name. " ". $saltern->saltern->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Submit Button -->
                            <div class="col-md-6 d-flex align-items-end mt-1">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Loans</h3>
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
                                    <th>Waikal</th>
                                    <th>Requested Amount</th>
                                    <th>Approved Amount</th>
                                    <th>Outstanding</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loans as $loan)
                                <tr>
                                    <td>{{ $loan->formatted_date  }}</td>
                                    <td>{{ $loan->membership->owner->full_name }}</td>
                                    <td>{{ $loan->membership->saltern->yahai->side->name." ".$loan->membership->saltern->yahai->name." - ".$loan->membership->saltern->name }}</td>
                                    <td>{{ $loan->requested_amount }}</td>
                                    <td>{{ $loan->approved_amount }}</td>
                                    <td>{{ number_format($loan->approved_amount - $loan->ownerLoanRepayment->sum('amount') ?: 0, 2) }}</td>
                                    <td>@if($loan->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                        @else
                                        <span class="badge bg-secondary">{{ ucfirst($loan->status) }}</span>
                                        @endif
                                    </td>
                                    <td><a href="{{ route('owner.my-loans.show', $loan->id) }}"
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