@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Yahai')
@section('content_header_title', 'Weighbridge')
@section('content_header_subtitle', 'Welcome')
@section('plugins.Datatables', true)

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Weighbridge Entries</h3>
                    <a href="{{ route('weighbridge_entries.create') }}" class="btn btn-success float-right"> <i
                            class="fas fa-plus"></i> Create
                        Entry</a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table id="weighbridgeTable" class="table table-sm nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Turn</th>
                                    <th>Vehicle ID</th>
                                    <th>Weight</th>
                                    <th>Yahai</th>
                                    <th>Waikal</th>
                                    <th>bags</th>
                                    <th>Owner</th>
                                    <th>Buyer</th>
                                    <th>Amount</th>
                                    <th>Bill</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($entries as $entry)
                                <tr>
                                    <td>{{ $entry->transaction_date }}</td>
                                    <td>{{ $entry->turn_no  }}</td>
                                    <td>{{ $entry->vehicle_id }}</td>
                                    <td>{{ $entry->net_weight }}</td>
                                    <td>{{ $entry->membership->saltern->yahai->name }}</td>
                                    <td>{{ $entry->membership->saltern->name }}</td>
                                    <td>{{ $entry->bags_count ?? 'N/A' }}</td>
                                    <td>{{ $entry->owner->name_with_initial ?? 'N/A' }}</td>
                                    <td>{{ $entry->buyer->full_name ?? 'N/A' }}</td>
                                    <td>{{ $entry->total_amount ?? 'N/A' }}</td>
                                    <td> N/A </td>
                                    <td><a href="{{ route('weighbridge_entries.show', $entry->id) }}"
                                            class="btn btn-default btn-xs">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <form action="{{ route('weighbridge-entries.delete', $entry->id) }}"
                                            method="POST" style="display:inline;"
                                            onsubmit="return confirm('Are you sure you want to delete this entry?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash-alt"></i> Delete
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
    $('#weighbridgeTable').DataTable({ order: [[0, 'desc']],   pageLength: 50 });
});
</script>
@endpush