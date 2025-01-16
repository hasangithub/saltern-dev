@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Edit Waikal')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">Edit Waikal</h3>
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
        <form action="{{ route('saltern.update', $saltern) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="yahai_id">Yahai</label>
                <select name="yahai_id" id="yahai_id" class="form-control" required>
                    <option value="">Select Yahai</option>
                    @foreach($yahais as $yahai)
                    <option {{ $saltern->yahai_id == $yahai->id?'selected':'' }} value="{{ $yahai->id }}">{{ $yahai->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row">
                <div class="col-md-6">      
                    <div class="form-group">
                        <label for="name">Saltern Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="" value="{{ $saltern->name }}">
                    </div>
                </div>

            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="{{ route('saltern.index') }}" class="btn btn-danger ml-1">Cancel</a>
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