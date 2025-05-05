@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Loan')
@section('content_header_title', 'Request Loan')
@section('content_header_subtitle', 'Staff')

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
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form method="POST" action="{{ route('owner-loans.store') }}" autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="saltern_id">Membership</label>
                                    <select class="form-control" name="membership_id" id="membership_id" required>
                                        <option value="">Select Membership</option>
                                        @foreach($memberships as $membership)
                                        <option value="{{ $membership->id }}">{{ $membership->saltern->yahai->name." - ".$membership->saltern->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="requested_amount" class="form-label">Loan Amount</label>
                                    <input type="number" name="requested_amount" id="requested_amount" class="form-control"
                                        value="{{ old('requested_amount') }}">
                                </div>

                                <div class="mb-3">
                                    <label for="reason" class="form-label">Reason (Optional)</label>
                                    <textarea name="purpose" id="purpose"
                                        class="form-control">{{ old('reason') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class=""></i> Request Loan
                        </button>
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
function previewImage(event, previewElementId) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById(previewElementId);
        output.src = reader.result;
        output.style.display = 'block'; // Show the image
    }
    reader.readAsDataURL(event.target.files[0]); // Read the selected file
}
</script>
@endpush