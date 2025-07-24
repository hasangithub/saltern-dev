@extends('layout.app')

@section('subtitle', 'Welcome')
@section('content_header_title', 'Complaint')
@section('content_header_subtitle', 'Welcome')
@section('plugins.Datatables', true)

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Complaint Details Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Complaint Details</h3>
                </div>
                <div class="card-body">

                    {{-- Success / Error Messages --}}
                    @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                    @endif

                    {{-- Complaint Main Text --}}
                    <p><strong>Complaint:</strong> {{ $complaint->complaint_text }}</p>
                    <p><strong>Status:</strong> {{ ucfirst($complaint->status) }}</p>

                    {{-- Assigned Info --}}
                    @if ($complaint->user_assigned)
                        <p><strong>Assigned To:</strong> {{ $complaint->assignedUser->name }}</p>
                    @else
                        {{-- Only manager can assign --}}
                        @role('manager')
                        <form method="POST" action="{{ route('staff.complaints.assign', $complaint) }}">
                            @csrf
                            <div class="form-group">
                                <label for="user_id">Assign to Staff</label>
                                <select name="user_id" class="form-control" required>
                                    <option value="">-- Select Staff --</option>
                                    @foreach(App\Models\User::role('staff')->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="btn btn-sm btn-success">Assign</button>
                        </form>
                        @endrole
                    @endif

                    <hr>

                    {{-- Assigned user reply form --}}
                    @if (auth()->user()->hasRole('staff') && auth()->id() === $complaint->user_assigned && $complaint->status === 'in_progress')
                        @if (!$complaint->reply_text)
                            <form method="POST" action="{{ route('staff.complaints.reply', $complaint) }}">
                                @csrf
                                <div class="form-group">
                                    <label for="reply_text">Reply to Complaint</label>
                                    <textarea name="reply_text" class="form-control" rows="4" required></textarea>
                                </div>
                                <button class="btn btn-primary btn-sm mt-2">Submit Reply & Mark as Resolved</button>
                            </form>
                        @else
                            <p><strong>Reply:</strong> {{ $complaint->reply_text }}</p>
                        @endif
                    @else
                        @if ($complaint->reply_text)
                            <p><strong>Reply:</strong> {{ $complaint->reply_text }}</p>
                        @endif
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('css')
{{-- Custom styles if needed --}}
@endpush

@push('js')
{{-- JS for datatable, if required --}}
@endpush
