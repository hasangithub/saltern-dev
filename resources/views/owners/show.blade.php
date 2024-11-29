@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Owner')
@section('content_header_subtitle', 'Welcome')
@section('plugins.Datatables', true)

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-square"
                            src="{{ asset('storage/' . $membership->profile_picture) }}" alt="User profile picture">
                    </div>

                    <h3 class="profile-username text-center">{{ $membership->full_name }}</h3>

                    <p class="text-muted text-center">{{ $membership->nic }}</p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Memberships</b> <a class="float-right">0</a>
                        </li>
                    </ul>

                    <a href="#" class="btn btn-primary btn-block"><b>Memberships</b></a>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <div class="col-md-9">
            <!-- Owner Details Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Owner Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Name</strong>
                            <p class="text-muted">{{ $membership->full_name }}</p>
                            <hr>
                            <strong>Gender</strong>
                            <p class="text-muted"> {{ $membership->gender }}</p>
                            <hr>
                            <strong>Civil Status</strong>
                            <p class="text-muted"> {{ $membership->civil_status }}</p>
                            <hr>
                            <strong>Date of Birth</strong>
                            <p class="text-muted"> {{ $membership->date_of_birth }}</p>
                            <hr>
                        </div>
                        <div class="col-md-6">
                            <strong>Phone Number 1</strong>
                            <p class="text-muted">{{ $membership->phone_number }}</p>
                            <hr>
                            <strong>Phone Number 2</strong>
                            <p class="text-muted"> {{ $membership->secondary_phone_number }}</p>
                            <hr>
                            <strong>Email</strong>
                            <p class="text-muted"> {{ $membership->email }}</p>
                            <hr>
                            <strong>Address 1</strong>
                            <p class="text-muted"> {{ $membership->address_line_1 }}</p>
                            <hr>
                            <strong>Address 2</strong>
                            <p class="text-muted"> {{ $membership->address_line_2 }}</p>
                        </div>
                    </div>
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