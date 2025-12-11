@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Refunds')
@section('content_header_subtitle', 'Refunds')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container">
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <div class="card">
        <div class="card-body p-4">
        <form method="POST" action="{{ route('refunds.preview') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="from_date">From Date</label>
                        <input type="date" name="from_date" id="from_date" class="form-control" value="{{ old('from_date') }}" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="to_date">To Date</label>
                        <input type="date" name="to_date" id="to_date" class="form-control" value="{{ old('to_date') }}" required>
                    </div>
                    <div class="form-group col-md-3 align-self-end">
                        <button class="btn btn-primary">Search & Preview</button>
                        <a href="{{ route('refunds.history') }}" class="btn btn-secondary">Refund History</a>
                    </div>
                </div>
            </form>

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