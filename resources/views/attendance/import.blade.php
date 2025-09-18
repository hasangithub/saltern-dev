@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Import Attendance')
@section('content_header_title', 'Import Attendance')
@section('content_header_subtitle', 'Import Attendance')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
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
                    <div class="container">
                        <h4>Import Attendance File</h4>
                        <form action="{{ route('attendance.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="file">Upload XLSX File:</label>
                                <input type="file" name="file" required class="form-control">
                            </div>
                            <button class="btn btn-primary">Import Attendance</button>
                        </form>
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