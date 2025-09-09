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

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Pay Period</th>
                        <th>Status</th>
                        <th># Employees</th>
                        <th>Processed By</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $batch)
                    <tr>
                        <td>{{ $batch->pay_period }}</td>
                        <td><span
                                class="badge bg-{{ $batch->status === 'draft' ? 'secondary' : ($batch->status==='approved'?'info':'success') }}">{{ ucfirst($batch->status) }}</span>
                        </td>
                        <td>{{ $batch->payrolls_count }}</td>
                        <td>{{ optional($batch->processor)->name ?? '-' }}</td>
                        <td>{{ $batch->created_at->format('Y-m-d') }}</td>
                        <td class="text-end">
                            @if($batch->status === 'draft')
                            <a href="{{ route('payroll.batches.edit', $batch->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            @endif
                            <a class="btn btn-sm btn-warning"
                                href="{{ route('payroll.batches.show', $batch) }}">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center p-4">No batches yet.</td>
                    </tr>
                    @endforelse
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