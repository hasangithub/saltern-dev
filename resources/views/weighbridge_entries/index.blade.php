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
        <div class="col-lg-3 col-6">
            <div class="info-box shadow">
                <span class="info-box-icon bg-primary"><i class="far fa-copy"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Approved</span>
                    <span class="info-box-number">{{ $approvedCount }}</span>
                    <a href="{{ route('weighbridge_entries.index', ['status' => 'approved']) }}"
                        class="small-box-footer">
                        List <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
                <!-- /.info-box-content -->
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="info-box shadow">
                <span class="info-box-icon bg-warning"><i class="far fa-copy"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pending</span>
                    <span class="info-box-number">{{ $pendingCount }}</span>
                    <a href="{{ route('weighbridge_entries.index', ['status' => 'pending']) }}"
                        class="small-box-footer">
                        List <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
                <!-- /.info-box-content -->
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card {{$cardOutline}}">
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
                        <table id="weighbridgeTable" class="table table-sm" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Vehicle ID</th>
                                    <th>Initial Weight(Kg)</th>
                                    <th>Tare Weight(Kg)</th>
                                    <th>Yahai</th>
                                    <th>Saltern</th>
                                    <th>Owner</th>
                                    <th>Buyer</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($entries as $entry)
                                <tr>
                                    <td>{{ $entry->transaction_date }}</td>
                                    <td>{{ $entry->vehicle_id }}</td>
                                    <td>{{ $entry->initial_weight }}</td>
                                    <td> {!! $entry->tare_weight ?? '<span class="badge bg-warning">Pending</span>' !!}
                                    </td>
                                    <td>{{ $entry->membership->saltern->yahai->name }}</td>
                                    <td>{{ $entry->membership->saltern->name }}</td>
                                    <td>{{ $entry->owner->full_name ?? 'N/A' }}</td>
                                    <td>{{ $entry->buyer->full_name ?? 'N/A' }}</td>
                                    <td><a href="{{ route('weighbridge_entries.show', $entry->id) }}"
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
    $('#weighbridgeTable').DataTable();
});
</script>
@endpush