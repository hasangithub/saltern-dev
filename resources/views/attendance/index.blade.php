@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Attendance')
@section('content_header_title', 'Attendance')
@section('content_header_subtitle', 'Attendance')
@section('page-buttons')
<a href="{{ route('attendance.import') }}" class="btn btn-success btn-sm"> <i class="fas fa-plus"></i> Import Attendance</a>
@endsection

{{-- Content body: main page content --}}

@section('content_body')
<div class="container">
<form method="GET" class="mb-3">
    <input type="date" name="date" value="{{ request('date') }}">
    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
</form>
    <table class="table table-bordered table-sm table-hover">
        <thead>
            <tr>
                <th>Employee</th>
                <th>Date</th>
                <th>Status</th>
                <th>Punch Times</th>
                <th>Worked Hours</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $attendance)
            <tr>
                <td>{{ $attendance->user->name ?? 'N/A' }}</td>
                <td>{{ $attendance->attendance_date }}</td>
                <td>{{ $attendance->status ? 'Present' : 'Absent' }}</td>
                <td>
                    @if(!empty($attendance->punch_times))
                    {{ implode(', ', json_decode($attendance->punch_times)) }}
                    @else
                    -
                    @endif
                </td>
                <td>{{ $attendance->worked_hours ?? 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
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
    $('#buyersTable').DataTable();
});
</script>
@endpush