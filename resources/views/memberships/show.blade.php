@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Membership')
@section('content_header_subtitle', 'Welcome')
@section('plugins.Datatables', true)

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Owner Details Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Owner Details</h3>
                </div>
                <div class="card-body">
                    <strong>Name</strong>
                    <p class="text-muted">{{ $membership->owner->full_name }}</p>
                    <hr>
                    <strong>Email</strong>
                    <p class="text-muted"> {{ $membership->owner->email }}</p>
                    <hr>
                    <strong>Phone</strong>
                    <p class="text-muted"> {{ $membership->owner->phone }}</p>
                    <hr>
                    <strong>Membership Date</strong>
                    <p class="text-muted"> {{ $membership->membership_date }}</p>
                    <hr>
                    <strong>Signature</strong>
                    <p class="text-muted"></p>
                    @if($membership->owner_signature)
                    <img src="{{ asset('storage/' . $membership->owner_signature) }}" class="img-fluid"
                        alt="O Signature" style="width: 100px; height: auto;">
                    @else
                    <p>No signature uploaded.</p>
                    @endif
                    <hr>
                    <strong>Is Active:</strong> 
                    <p class="text-muted"> {{ $membership->is_active ? 'Yes' : 'No' }}</p>
                </div>
            </div>
            <!-- /.card -->
        </div>

        <div class="col-12">
            <!-- Representative Details Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Representative Details</h3>
                </div>
                <div class="card-body">

                    <strong> Name</strong>

                    <p class="text-muted">
                        {{ $membership->representative_name }}
                    </p>

                    <hr>

                    <strong> Signature</strong>

                    <p class="text-muted">
                        @if($membership->representative_signature)
                        <img src="{{ asset('storage/' . $membership->representative_signature) }}" class="img-fluid"
                            alt="Representative Signature" style="width: 100px; height: auto;">
                        @else
                    <p>No signature uploaded.</p>
                    @endif
                    </p>
                </div>
            </div>
            <!-- /.card -->
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
    $('#buyersTable').DataTable();
});
</script>
@endpush