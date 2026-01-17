@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Settings')
@section('content_header_subtitle', 'Settings')
@section('page-buttons')

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
                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf

                        <div class="form-check">
                            <input type="checkbox" name="weighbridge_manual_enable" id="manualEnable"
                                class="form-check-input" value="1" {{ $manualEnabled == 1 ? 'checked' : '' }}>
                            <label for="manualEnable" class="form-check-label">Enable Manual Weighbridge Input</label>
                        </div>

                        <button type="submit" class="btn btn-success mt-2">Save Settings</button>
                    </form>
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
    $('#ownersTable').DataTable({
        order: [
            [0, 'desc']
        ],
        pageLength: 50
    });
});
</script>
@endpush