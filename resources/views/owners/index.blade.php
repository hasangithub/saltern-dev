@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Owners')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Owner List</h3>
                    <a href="{{ route('owners.create') }}" class="btn btn-success ml-auto"> <i class="fas fa-plus"></i> Create Owner</a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table id="ownersTable" class="table table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Full Name</th>
                                    <th>Date of Birth</th>
                                    <th>NIC</th>
                                    <th>Address</th>
                                    <th>Mobile No</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($owners as $owner)
                                <tr>
                                    <td>{{ $owner->id }}</td>
                                    <td>{{ $owner->full_name }}</td>
                                    <td>{{ $owner->dob }}</td>
                                    <td>{{ $owner->nic }}</td>
                                    <td>{{ $owner->address }}</td>
                                    <td>{{ $owner->mobile_no }}</td>
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
    $('#ownersTable').DataTable();
});
</script>
@endpush