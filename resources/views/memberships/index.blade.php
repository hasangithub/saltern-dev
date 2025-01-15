@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Memberships')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Memberships List</h3>
                    <a href="{{ route('memberships.create') }}" class="btn btn-success ml-auto"> <i
                            class="fas fa-plus"></i> Create Membership</a>
                </div>

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
                                    <th>ID</th>
                                    <th>Membership No</th>
                                    <th>Yahai</th>
                                    <th>Saltern</th>
                                    <th>Side</th>
                                    <th>Owner</th>
                                    <th>Address</th>
                                    <th>Mobile</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($memberships as $membership)
                                <tr>
                                    <td>{{ $membership->id }}</td>
                                    <td>{{ $membership->membership_no }}</td>
                                    <td>{{ $membership->saltern->yahai->name }}</td>
                                    <td>{{ $membership->saltern->name }}</td>
                                    <td>{{ $membership->saltern->yahai->side->name }}</td>
                                    <td>{{ $membership->owner->full_name }}</td>
                                    <td>{{ $membership->owner->address_line_1 }}</td>
                                    <td>{{ $membership->owner->phone_number }}</td>
                                    <td><a href="{{ route('memberships.show', $membership->id) }}"
                                            class="btn btn-default btn-xs">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="{{ route('memberships.edit', $membership->id) }}"
                                            class="btn btn-warning btn-xs">
                                            <i class="fas fa-edit"></i> Edit
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
    $('#membershipsTable').DataTable();
});
</script>
@endpush