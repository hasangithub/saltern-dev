@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Membership')
@section('content_header_title', 'Create Membership')
@section('content_header_subtitle', 'Membership')

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
                    <form method="POST" action="{{ route('memberships.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="saltern_id">Saltern</label>
                            <select class="form-control" name="saltern_id" id="saltern_id" required>
                                <option value="">Select Saltern</option>
                                @foreach($salterns as $saltern)
                                <option value="{{ $saltern->id }}">{{ $saltern->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="owner_id">Owner</label>
                            <select class="form-control" name="owner_id" id="owner_id" required>
                                <option value="">Select Owner</option>
                                @foreach($owners as $owner)
                                <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="membership_date">Membership Date</label>
                            <input type="date" class="form-control" name="membership_date" id="membership_date"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="owner_signature">Owner Signature</label>
                            <input type="file" class="form-control" name="owner_signature" id="owner_signature"
                                accept="image/*" required onchange="previewImage(event, 'ownerSignaturePreview')">
                            <img id="ownerSignaturePreview" src="#" alt="Owner Signature Preview"
                                style="display:none; width:200px; margin-top:10px;">
                        </div>

                        <div class="form-group">
                            <label for="representative_name">Representative Name</label>
                            <input type="text" class="form-control" name="representative_name" id="representative_name"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="representative_signature">Representative Signature</label>
                            <input type="file" class="form-control" name="representative_signature"
                                id="representative_signature" accept="image/*" required
                                onchange="previewImage(event, 'representativeSignaturePreview')">
                            <img id="representativeSignaturePreview" src="#" alt="Representative Signature Preview"
                                style="display:none; width:200px; margin-top:10px;">
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" name="is_active" id="is_active" value="1">
                            <label class="form-check-label" for="is_active">Active Membership</label>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Create Membership
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