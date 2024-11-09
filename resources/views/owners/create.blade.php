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
                                    <input type="date" name="date_of_birth" id="dob" class="form-control" required>
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
                                <!-- Phone Number -->
                                <div class="form-group">
                                    <label for="phone_number">Phone Number</label>
                                    <input type="text" name="phone_number" id="phone_number" class="form-control"
                                        value="{{ old('phone_number') }}" required>
                                </div>

                                <!-- Secondary Phone Number -->
                                <div class="form-group">
                                    <label for="secondary_phone_number">Secondary Phone Number</label>
                                    <input type="text" name="secondary_phone_number" id="secondary_phone_number"
                                        class="form-control" value="{{ old('secondary_phone_number') }}">
                                </div>

                                <!-- Email -->
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" id="email" class="form-control"
                                        value="{{ old('email') }}">
                                </div>

                                <!-- Address Line 1 -->
                                <div class="form-group">
                                    <label for="address_line_1">Address Line 1</label>
                                    <input type="text" name="address_line_1" id="address_line_1" class="form-control"
                                        value="{{ old('address_line_1') }}" required>
                                </div>

                                <!-- Address Line 2 -->
                                <div class="form-group">
                                    <label for="address_line_2">Address Line 2</label>
                                    <input type="text" name="address_line_2" id="address_line_2" class="form-control"
                                        value="{{ old('address_line_2') }}">
                                </div>

                                <!-- Profile Picture -->
                                <!-- <div class="form-group">
                                    <label for="profile_picture">Profile Picture</label>
                                    <input type="file" name="profile_picture" id="profile_picture" class="form-control"
                                        value="{{ old('profile_picture') }}">
                                </div> -->

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