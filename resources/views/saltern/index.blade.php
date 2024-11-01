@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Saltern')
@section('content_header_title', 'Saltern')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container">
    <div class="card card-default">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Saltern List</h3>
            <a href="{{ route('saltern.create') }}" class="btn btn-success ml-auto"> <i class="fas fa-plus"></i> Create Saltern</a>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <table id="yahaiTable" class="table table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Owner</th>
                                <th>Yahai</th>
                                <th>Saltern</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salterns as $saltern)
                            <tr>
                                <td>{{ $saltern->owner->full_name }}</td>
                                <td>{{ $saltern->yahai->name }}</td>
                                <td>{{ $saltern->name }}</td>
                                <td><a class="btn btn-info" href="{{ route('saltern.edit',$saltern) }}">Edit</a>
                                    <form action="{{ route('saltern.destroy', $saltern) }}" method="POST" class="d-inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger">Delete</button>
                                    </form>  
                                    
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