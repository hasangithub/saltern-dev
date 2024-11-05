@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Create Owner')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Create new owner</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form action="{{ route('owners.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="full_name">Full Name</label>
                                    <input type="text" name="full_name" id="full_name" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="dob">Date of Birth</label>
                                    <input type="date" name="dob" id="dob" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="nic">NIC</label>
                                    <input type="text" name="nic" id="nic" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select name="gender" class="form-control" required>
                                        @foreach($genders as $gender)
                                        <option value="{{ $gender->value }}">{{ $gender->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="civil_status">Civil Status</label>
                                    <select name="civil_status" class="form-control" required>
                                        @foreach($civilStatuses as $status)
                                        <option value="{{ $status->value }}">{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <input type="text" name="address" id="address" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="mobile_no">Mobile No</label>
                                    <input type="text" name="mobile_no" id="mobile_no" class="form-control" required>
                                </div>
                            </div>

                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save
                        </button>
                    </form>
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
console.log("Hi, I'm using the Laravel-AdminLTE package!");
</script>
@endpush