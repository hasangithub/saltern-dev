@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'payrolls')
@section('content_header_subtitle', 'payrolls')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Payroll Batches</h4>
        <a href="{{ route('payroll.batches.create') }}" class="btn btn-primary">Create New Batch</a>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <form method="POST" action="{{ route('payroll.batches.store') }}" class="card card-body">
        @csrf

        <div class="mb-3">
            <label class="form-label">Pay Period (YYYY-MM)</label>
            <input type="text" name="pay_period" class="form-control @error('pay_period') is-invalid @enderror"
                placeholder="2025-08" value="{{ old('pay_period') }}">
            @error('pay_period') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <div class="form-text">Example: 2025-08</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Batch Type</label>
            <select name="batch_type" class="form-control @error('batch_type') is-invalid @enderror" required>
                <option value="">-- Select Type --</option>
                <option value="permanent" {{ old('batch_type') == 'permanent' ? 'selected' : '' }}>Permanent</option>
                <option value="first_half" {{ old('batch_type') == 'first_half' ? 'selected' : '' }}>First Half</option>
                <option value="second_half" {{ old('batch_type') == 'second_half' ? 'selected' : '' }}>Second Half
                </option>
            </select>
            @error('batch_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('payroll.batches.index') }}" class="btn btn-light">Cancel</a>
            <button type="submit" class="btn btn-primary">Create & Continue</button>
        </div>
    </form>
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