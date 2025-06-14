@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Waikal')
@section('content_header_title', 'Waikal')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container">
    <div class="card card-default">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Waikal No</h3>
            <a href="{{ route('saltern.create') }}" class="btn btn-success ml-auto"> <i class="fas fa-plus"></i> Create Waikal</a>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <table id="salternTable" class="table table-sm" style="width:100%">
                        <thead>
                            <tr>
                                <th>Yahai</th>
                                <th>Waikal No</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salterns as $saltern)
                            <tr>
                                <td>{{ $saltern->yahai->name }}</td>
                                <td>{{ $saltern->name }}</td>
                                <td><a class="btn btn-warning btn-xs" href="{{ route('saltern.edit',$saltern) }}"> <i class="fas fa-edit"></i> Edit</a> 
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
        $('#salternTable').DataTable();
    });
</script>
@endpush