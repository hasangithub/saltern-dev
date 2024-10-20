@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Yahai')
@section('content_header_title', 'Weighbridge')
@section('content_header_subtitle', 'Welcome')
@section('plugins.Datatables', true)

{{-- Content body: main page content --}}

@section('content_body')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Weighbridge Entries</h3>
            <a href="{{ route('weighbridge_entries.create') }}" class="btn btn-primary float-right">Create Entry</a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <table id="weighbridgeTable" class="table table-bordered table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Vehicle ID</th>
                        <th>Initial Weight</th>
                        <th>Tare Weight</th>
                        <th>Transaction Date</th>
                        <th>Owner</th>
                        <th>Buyer</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $entry)
                    <tr>
                        <td>{{ $entry->id }}</td>
                        <td>{{ $entry->vehicle_id }}</td>
                        <td>{{ $entry->initial_weight }}</td>
                        <td>{{ $entry->tare_weight }}</td>
                        <td>{{ $entry->transaction_date }}</td>
                        <td>{{ $entry->owner->full_name ?? 'N/A' }}</td>
                        <td>{{ $entry->buyer->name ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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