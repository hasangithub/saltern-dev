@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Employees')
@section('content_header_subtitle', 'Welcome')
@section('page-buttons')
<a href="{{ route('employees.create') }}" class="btn btn-success ml-auto"> <i
                            class="fas fa-plus"></i> Create Employee</a>
@endsection
{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table id="membershipsTable" class="table table-sm" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Employee Id</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Designation</th>
                                    <th>Roles</th>
                                    <th>Joining Date</th>
                                    <th>Salary</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $index => $employee)
                                <tr>
                                    <td>{{ $employee->id }}</td>
                                    <td>{{ $employee->user->name }}</td>
                                    <td>{{ $employee->user->email }}</td>
                                    <td>{{ $employee->designation }}</td>
                                    <td>
                                        @foreach($employee->user->getRoleNames() as $role)
                                        <span class="badge bg-success">{{ $role }}</span>
                                        @endforeach
                                    </td>
                                    <td>{{ $employee->join_date }}</td>
                                    <td>{{ number_format($employee->base_salary, 2) }}</td>
                                    <td><a href="{{ route('employees.edit', $employee->user->id) }}"
                                            class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a></td>
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
    $('#membershipsTable').DataTable();
});
</script>
@endpush