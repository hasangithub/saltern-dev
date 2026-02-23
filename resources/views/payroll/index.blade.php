@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'payrolls')
@section('content_header_subtitle', 'payrolls')
@section('page-buttons')
<a href="{{ route('payroll.batches.create') }}" class="btn btn-primary">Create New Batch</a>
@endsection
{{-- Content body: main page content --}}

@section('content_body')
<div class="container">


    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <div class="card mb-3">
        <div class="card-header">
            <strong>Select Pay Period</strong>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('payroll.batches.index') }}">
                <div class="row align-items-end">

                    <div class="col-md-4">
                        <label>Pay Period</label>
                        <select name="period" class="form-control">
                            @foreach($periods as $period)
                            <option value="{{ $period }}" {{ $currentPeriod == $period ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $period)->format('F Y') }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            Filter
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Payroll Batches</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Pay Period</th>
                            <th>Status</th>
                            <th>Type</th>
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
                            <td>{{ optional($batch->payrollTemplate)->name ?? '—' }}</td>
                            <td>{{ $batch->payrolls_count }}</td>
                            <td>{{ optional($batch->processor)->name ?? '-' }}</td>
                            <td>{{ $batch->created_at->format('Y-m-d') }}</td>
                            <td class="text-end">
                                @if($batch->status === 'draft')
                                @if(optional($batch->payrollTemplate)->name === 'Permanent')
                                <a href="{{ route('payroll.batches.edit', $batch->id) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @else
                                <a href="{{ route('payroll.batches.contractEdit', $batch->id) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @endif
                                @endif
                                @if(optional($batch->payrollTemplate)->name === 'Permanent')
                                <a class="btn btn-sm btn-warning" href="{{ route('payroll.batches.show', $batch) }}">
                                    View
                                </a>
                                @else
                                <a class="btn btn-sm btn-warning"
                                    href="{{ route('payroll.batches.contractShow', $batch) }}">
                                    View
                                </a>
                                @endif
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