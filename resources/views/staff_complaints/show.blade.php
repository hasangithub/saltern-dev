@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Complaint')
@section('content_header_subtitle', 'Welcome')
@section('plugins.Datatables', true)

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Owner Details Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Complaint Details</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <p>{{ $complaint->text }}</p>
                    <p>Status: {{ $complaint->status }}</p>

                    @if ($complaint->user_assigned)
                    <p>Assigned to: {{ $complaint->assignedUser->name }}</p>
                    @else
                    <form method="POST" action="{{ route('staff.complaints.assign', $complaint) }}">
                        @csrf
                        <select name="user_id" required>
                            @foreach (\App\Models\User::whereIn('id', [2,3,4])->get() as $user)
                            {{-- example of restricted users --}}
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <button>Assign</button>
                    </form>
                    @endif

                    @if (!$complaint->reply_text && $complaint->status == 'in_progress' && auth()->id() === $complaint->user_assigned)
                    <form method="POST" action="{{ route('staff.complaints.reply', $complaint) }}">
                        @csrf
                        <textarea name="reply_text" class="form-control" required></textarea>
                        <br>
                        <button class="btn btn-primary">Reply</button>
                    </form>
                    @else
                    <p>Reply: {{ $complaint->reply_text }}</p>
                    @endif
                </div>
            </div>
            <!-- /.card -->
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
    $('#buyersTable').DataTable();
});
</script>
@endpush