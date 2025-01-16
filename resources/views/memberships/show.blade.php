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
        <div class="col-md-3">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-square"
                            src="{{ asset('storage/' . $membership->owner->profile_picture) }}"
                            alt="Owner profile picture">
                    </div>

                    <h3 class="profile-username text-center">{{ $membership->owner->full_name }}</h3>

                    <p class="text-muted text-center">{{ $membership->owner->nic }}</p>

                    <a href="#" class="btn btn-primary btn-block"><b>Owner Profile</b></a>
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
                        <div class="col-md-4">
                            <strong>Name</strong>
                            <p class="text-muted">{{ $membership->owner->full_name }}</p>
                            <hr>
                            <strong>Gender</strong>
                            <p class="text-muted"> {{ $membership->owner->gender }}</p>
                            <hr>
                           
                            <strong>Date of Birth</strong>
                            <p class="text-muted"> {{ $membership->owner->date_of_birth }}</p>
                            <hr>
                        </div>
                        <div class="col-md-4">
                            <strong>Phone Number 1</strong>
                            <p class="text-muted">{{ $membership->owner->phone_number }}</p>
                            <hr>
                            <strong>Whatsapp Number </strong>
                            <p class="text-muted"> {{ $membership->owner->whatsapp_number }}</p>
                            <hr>
                            <strong>Email</strong>
                            <p class="text-muted"> {{ $membership->owner->email }}</p>
                            <hr>
                            <strong>Address </strong>
                            <p class="text-muted"> {{ $membership->owner->address_line_1 }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Membership No</strong>
                            <p class="text-muted"> {{ $membership->membership_no }}</p>
                            <hr>
                            <strong>Membership Issued</strong>
                            <p class="text-muted">{{ $membership->membership_date }}</p>
                            <hr>
                            <strong>Status</strong>
                            <p class="text-muted"> {{ $membership->is_active }}</p>
                            <hr>
                            <strong>Email</strong>
                            <p class="text-muted"> {{ $membership->owner->email }}</p>
                            <hr>
                            <strong>Owner Signature</strong>
                            @if($membership->owner_signature)
                            <p>
                                <img src="{{ asset('storage/' . $membership->owner_signature) }}" class="img-fluid"
                                    alt="O Signature" style="width: 100px; height: auto;">
                            </p>
                            @else
                            <p>No signature uploaded.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">

        </div>
        <div class="col-md-9">
            <!-- Owner Details Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Representative Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Name with Initial</strong>
                            <p class="text-muted">{{ $membership->representative->name_with_initial }}</p>
                            <hr>
                            <strong>NIC</strong>
                            <p class="text-muted"> {{ $membership->representative->nic }}</p>
                            <hr>
                            <strong>Phone Number 1</strong>
                            <p class="text-muted">{{ $membership->representative->phone_number }}</p>
                            <hr>
                            <strong>Relationship</strong>
                            <p class="text-muted"> {{ $membership->representative->relationship }}</p>
                            <hr>
                            <strong>Representative Signature</strong>
                            @if($membership->representative_signature)
                            <p>
                                <img src="{{ asset('storage/' . $membership->representative_signature) }}"
                                    class="img-fluid" alt="R Signature" style="width: 100px; height: auto;">
                            </p>
                            @else
                            <p>No signature uploaded.</p>
                            @endif
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