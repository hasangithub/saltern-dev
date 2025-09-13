@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Create Employee')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">Create Employee</h3>
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
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif
        <form action="{{ route('employees.store') }}" method="POST">
            @csrf
            <div class="form-group row">
                <label for="full_name" class="col-sm-3 col-form-label">Full Name</label>
                <div class="col-sm-9">
                    <input type="text" name="full_name" id="full_name" class="form-control"
                        value="{{ old('full_name') }}" autocomplete="off" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="email" class="col-sm-3 col-form-label">Email</label>
                <div class="col-sm-9">
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required
                        autocomplete="off">
                </div>
            </div>
            <div class="form-group row">
                <label for="password" class="col-sm-3 col-form-label">Password</label>
                <div class="col-sm-9">
                    <input type="password" name="password" id="password" class="form-control"
                        value="{{ old('password') }}" required autocomplete="off" required>
                </div>
            </div>

            <div class="form-group row">
                <label for="designation" class="col-sm-3 col-form-label">Designation</label>
                <div class="col-sm-9">
                    <select name="designation" class="form-control" required>
                        <option value="">Select Designation</option>
                        @foreach(\App\Models\Employee::designations() as $designation)
                        <option value="{{ $designation }}">
                            {{ $designation }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="employment_type" class="col-sm-3 col-form-label">Employment Type</label>
                <div class="col-sm-9">
                    <select name="employment_type" id="employment_type" class="form-control">
                        <option value="">-- Select Employment Type --</option>
                        <option value="permanent"
                            {{ old('employment_type', $employee->employment_type ?? '') == 'permanent' ? 'selected' : '' }}>
                            Permanent</option>
                        <option value="contract"
                            {{ old('employment_type', $employee->employment_type ?? '') == 'contract' ? 'selected' : '' }}>
                            Contract</option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="department" class="col-sm-3 col-form-label">Department</label>
                <div class="col-sm-9">
                    <select name="department" id="department" class="form-control">
                        <option value="">-- Select Department --</option>
                        <option value="office"
                            {{ old('department', $employee->department ?? '') == 'office' ? 'selected' : '' }}>Office
                        </option>
                        <option value="workshop"
                            {{ old('department', $employee->department ?? '') == 'workshop' ? 'selected' : '' }}>
                            Workshop
                        </option>
                        <option value="security"
                            {{ old('department', $employee->department ?? '') == 'security' ? 'selected' : '' }}>
                            Security
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="base_salary" class="col-sm-3 col-form-label">Base Salary</label>
                <div class="col-sm-9">
                    <input type="number" step='0.01' class="form-control" id="base_salary" name="base_salary" required
                        autocomplete="off">
                </div>
            </div>

            <div class="form-group row">
                <label for="epf_number" class="col-sm-3 col-form-label">EPF No</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="epf_number" name="epf_number"
                        value="" required>
                </div>
            </div>

            <div class="form-group row">
                <label for="join_date" class="col-sm-3 col-form-label">Join Date</label>
                <div class="col-sm-9">
                    <input type="date" class="form-control" id="join_date" name="join_date" autocomplete="off" required>
                </div>
            </div>

            <div class="form-group row">
                <label for="employment_status" class="col-sm-3 col-form-label">Employment Status</label>
                <div class="col-sm-9">
                    <select class="form-control" id="employment_status" name="employment_status" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                        <option value="Resigned">Resigned</option>
                        <option value="Terminated">Terminated</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Register Employee</button>
        </form>
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

@endpush