@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Edit Yahai')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">Edit Yahai</h3>
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
        <form action="{{ route('yahai.update', $yahai) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="side">Side</label>
                        <select name="side_id" id="side_id" class="form-control" required>
                            <option value="">Select Side</option>
                            @foreach ($sides as $side)
                            <option value="{{ $side->id }}" {{ $yahai->side_id == $side->id ? 'selected' : '' }}>
                                {{ ucfirst($side->name) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Yahai Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder=""
                            value="{{ $yahai->name }}">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('yahai.index') }}" class="btn btn-danger ml-1">Cancel</a>
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