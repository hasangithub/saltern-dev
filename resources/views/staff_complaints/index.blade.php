@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Complaints')
@section('content_header_subtitle', 'Complaints')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Owner Complaints</h3>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table id="membershipsTable" class="table table-sm table-bordered table-hover"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>Owner</th>
                                    <th>Complaint</th>
                                    <th>Voice File</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th> Action </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($complaints as $complaint)
                                @php
                                $createdAt = \Carbon\Carbon::parse($complaint->created_at);
                                $isOldAndUnresolved = $createdAt->diffInDays(now()) > 7 && $complaint->status !==
                                'resolved';
                                @endphp
                                <tr>
                                    <td>{{ $complaint->owner->name_with_initial }}</td>
                                    <td>{{ $complaint->complaint_text }}</td>
                                    <td>
                                        @if ($complaint->complaint_voice)
                                        <audio controls>
                                            <source src="{{ asset('storage/' . $complaint->complaint_voice) }}"
                                                type="audio/webm">
                                            Your browser does not support the audio element.
                                        </audio>
                                        @else
                                        N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if ($complaint->status === 'resolved')
                                        <span class="badge bg-success">Resolved</span>
                                        @elseif ($complaint->status === 'inprogress')
                                        <span class="badge bg-info text-dark">In Progress</span>
                                        @if ($isOldAndUnresolved)
                                        <span class="badge bg-danger ms-1">Overdue</span>
                                        @endif
                                        @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                        @if ($isOldAndUnresolved)
                                        <span class="badge bg-danger ms-1">Overdue</span>
                                        @endif
                                        @endif
                                    </td>

                                    <td>{{ $complaint->created_at }}</td>
                                    <td>
                                        <a href="{{ route('staff.complaints.show', $complaint) }}"
                                            class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
    $('#membershipsTable').DataTable();
});
</script>
@endpush