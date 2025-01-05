@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Yahai')
@section('content_header_title', 'Yahai')
@section('content_header_subtitle', 'Welcome')
@section('plugins.Datatables', true)

{{-- Content body: main page content --}}

@section('content_body')
<div class="container">
    <div class="card card-default">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Yahai List</h3>
            <a href="{{ route('yahai.create') }}" class="btn btn-success ml-auto"> <i class="fas fa-plus"></i> Create Yahai</a>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <table id="yahaiTable" class="table table-sm" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Side</th>
                                <th>CreatedAt</th>
                                <th>UpdatedAt</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($yahais as $yahai)
                            <tr>
                                <td>{{ $yahai->id }}</td>
                                <td>{{ $yahai->name }}</td>
                                <td>{{ $yahai->side }}</td>
                                <td>{{ $yahai->created_at }}</td>
                                <td>{{ $yahai->updated_at }}</td>
                                <td><a class="btn btn-warning btn-xs" href="{{ route('yahai.edit',$yahai) }}"> <i class="fas fa-edit"></i> Edit</a>      
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
        $('#yahaiTable').DataTable();
    });
</script>
@endpush