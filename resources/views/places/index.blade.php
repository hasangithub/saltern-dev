@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Places')
@section('content_header_subtitle', 'Places')
@section('page-buttons')
<a href="{{ route('places.create') }}" class="btn btn-primary">Create Place</a>
@endsection
{{-- Content body: main page content --}}

@section('content_body')
<div class="container">
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <div class="card">
        <div class="card-body p-4">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($places as $place)
                    <tr>
                        <td>{{ $place->name }}</td>
                        <td>{{ $place->description }}</td>
                        <td>
                            <a href="{{ route('places.edit', $place->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <!-- <form action="{{ route('places.destroy', $place->id) }}" method="POST"
                                style="display:inline-block">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure?')">Delete</button>
                            </form> -->
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

    <div class="mt-3">

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