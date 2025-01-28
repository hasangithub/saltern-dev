@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Yahai')
@section('content_header_title', 'Productions')
@section('content_header_subtitle', 'Welcome')
@section('plugins.Datatables', true)

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('productions.index') }}">
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
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" id="start_date" name="start_date" class="form-control"
                                    value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" id="end_date" name="end_date" class="form-control"
                                    value="{{ request('end_date') }}">
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
                <div class="card-header">
                    <h3 class="card-title">Production Details</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="weighbridgeTable" class="table table-sm" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Buyer</th>
                                    <th>Culture</th>
                                    <th>NetWeight</th>
                                    <th>Bags</th>
                                    <th>Service Charge (100%)</th>
                                    <th>Accumulated(30%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productions as $entry)
                                <tr>
                                    <td>{{ $entry->transaction_date }}</td>
                                    <td>{{ $entry->buyer->full_name ?? 'N/A' }}</td>
                                    <td>{{ $entry->culture ?? 'N/A' }}</td>
                                    <td>{{ $entry->net_weight }}</td>
                                    <td>{{ $entry->bags_count }}</td>
                                    <td>{{ $entry->total_amount }}</td>
                                    <td>{{ $entry->total_amount * 0.3 }}</td>
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
    $('#weighbridgeTable').DataTable();
});
</script>
@endpush