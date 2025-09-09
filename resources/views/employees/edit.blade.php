@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Create Employee')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">Edit Employee</h3>
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
        <form action="{{ route('employees.update', $employee->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group row">
                <label for="full_name" class="col-sm-3 col-form-label">Full Name</label>
                <div class="col-sm-9">
                    <input type="text" name="full_name" id="full_name" class="form-control"
                        value="{{ old('full_name', $employee->name) }}" required>
                </div>
            </div>

            <div class="form-group row">
                <label for="email" class="col-sm-3 col-form-label">Email</label>
                <div class="col-sm-9">
                    <input type="email" name="email" id="email" class="form-control"
                        value="{{ old('email', $employee->email) }}" required autocomplete="new-email">
                </div>
            </div>

            <div class="form-group row">
                <label for="password" class="col-sm-3 col-form-label">Password</label>
                <div class="col-sm-9">
                    <input type="password" name="password" id="password" class="form-control"
                        placeholder="Leave blank to keep current password" autocomplete="new-password">
                </div>
            </div>

            <div class="form-group row">
                <label for="designation" class="col-sm-3 col-form-label">Designation</label>
                <div class="col-sm-9">
                    <select name="designation" class="form-control" required>
                        <option value="">Select Designation</option>
                        @foreach(\App\Models\Employee::designations() as $designation)
                        <option value="{{ $designation }}"
                            {{ old('designation', $employee->employee->designation ?? '') == $designation ? 'selected' : '' }}>
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
                            {{ old('employment_type', $employee->employee->employment_type ?? '') == 'permanent' ? 'selected' : '' }}>
                            Permanent</option>
                        <option value="contract"
                            {{ old('employment_type', $employee->employee->employment_type ?? '') == 'contract' ? 'selected' : '' }}>
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
                            {{ old('department', $employee->employee->department ?? '') == 'office' ? 'selected' : '' }}>Office
                        </option>
                        <option value="workshop"
                            {{ old('department', $employee->employee->department ?? '') == 'workshop' ? 'selected' : '' }}>
                            Workshop
                        </option>
                        <option value="security"
                            {{ old('department', $employee->employee->department ?? '') == 'security' ? 'selected' : '' }}>
                            Security
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="base_salary" class="col-sm-3 col-form-label">Base Salary</label>
                <div class="col-sm-9">
                    <input type="number" step='0.01' class="form-control" id="base_salary" name="base_salary"
                        value="{{ old('base_salary', $employee->employee->base_salary) }}" required>
                </div>
            </div>

            <div class="form-group row">
                <label for="join_date" class="col-sm-3 col-form-label">Join Date</label>
                <div class="col-sm-9">
                    <input type="date" class="form-control" id="join_date" name="join_date"
                        value="{{ old('join_date', $employee->employee->join_date) }}" required>
                </div>
            </div>

            <div class="form-group row">
                <label for="employment_status" class="col-sm-3 col-form-label">Employment Status</label>
                <div class="col-sm-9">
                    <select class="form-control" id="employment_status" name="employment_status" required>
                        @foreach(['Active', 'Inactive', 'Resigned', 'Terminated'] as $status)
                        <option value="{{ $status }}"
                            {{ old('employment_status', $employee->employee->employment_status) == $status ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="roles" class="col-sm-3 col-form-label">Roles</label>
                <div class="col-sm-9">
                    @foreach($roles as $role)
                    <label>
                        <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                            {{ $employee->hasRole($role->name) ? 'checked' : '' }}>
                        {{ $role->name }}
                    </label>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="btn btn-success">Update Employee</button>
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