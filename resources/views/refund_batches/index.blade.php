@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Owners')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">

    <div class="d-flex justify-content-between mb-3">
        <h4>Service Charge Refund Batches</h4>
        <button class="btn btn-primary" data-toggle="modal" data-target="#createBatchModal">
            <i class="fa fa-plus"></i> Create Batch
        </button>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Name</th>
                        <th>Date Range</th>
                        <th>%</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($batches as $batch)
                    <tr>
                        <td>{{ $batch->name }}</td>
                        <td>{{ $batch->date_from }} → {{ $batch->date_to }}</td>
                        <td>{{ $batch->refund_percentage }}%</td>
                        <td>
                            <span class="badge badge-{{ 
                                $batch->status == 'draft' ? 'warning' :
                                ($batch->status == 'approved' ? 'info' :
                                ($batch->status == 'posted' ? 'success' : 'danger')) }}">
                                {{ strtoupper($batch->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('refund-batches.show',$batch->id) }}"
                               class="btn btn-sm btn-outline-primary">
                               View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@include('refund_batches.create_modal')
@stop

{{-- Push extra CSS --}}

@push('css')
{{-- Add here extra stylesheets --}}
{{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@endpush

{{-- Push extra scripts --}}

@push('js')
<script>

</script>
@endpush