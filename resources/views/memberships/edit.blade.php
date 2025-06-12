@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Membership')
@section('content_header_title', 'Edit Membership')
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
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form method="POST" action="{{ route('memberships.update', $membership->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="saltern_id">Saltern</label>
                                    <select class="form-control select2" name="membership[saltern_id]" id="saltern_id" required>
                                        <option value="">Select Saltern</option>
                                        @foreach($salterns as $saltern)
                                        <option {{ $membership->saltern_id == $saltern->id?'selected':'' }}
                                            value="{{ $saltern->id }}">{{ $saltern->yahai->side->name." ".$saltern->yahai->name." ".$saltern->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="owner_id">Owner</label>
                                    <select class="form-control select2" name="membership[owner_id]" id="owner_id" required>
                                        <option value="">Select Owner</option>
                                        @foreach($owners as $owner)
                                        <option {{ $membership->owner_id == $owner->id?'selected':'' }}
                                            value="{{ $owner->id }}">{{ $owner->name_with_initial }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="membership_date">Membership Date</label>
                                    <input type="date" class="form-control" name="membership[membership_date]"
                                        id="membership_date"
                                        value="{{ old('membership.membership_date', $membership->membership_date) }}"
                                        required>
                                </div>

                                <div class="form-group">
                                    <label for="owner_signature">Owner Signature</label>
                                    <input type="file" class="form-control" name="membership[owner_signature]"
                                        id="owner_signature" accept="image/*"
                                        onchange="previewImage(event, 'ownerSignaturePreview')">
                                    <img id="ownerSignaturePreview" src="#" alt="Owner Signature Preview"
                                        style="display:none; width:200px; margin-top:10px;">
                                </div>

                                <div class="form-group form-check">
                                    <input type="hidden" name="membership[is_active]" value="0">
                                    <input type="checkbox" class="form-check-input" name="membership[is_active]"
                                        id="is_active" value="1" {{ $membership->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active Membership</label>
                                </div>
                            </div>
                        </div>
                        <h3>Representative Details</h3>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name_with_initial">Name with Initial</label>
                                    <input type="text" name="representative[name_with_initial]" id="name_with_initial"
                                        class="form-control"
                                        value="{{ old('representative.name_with_initial', $membership->representative?->name_with_initial) }}">
                                </div>

                                <div class="form-group">
                                    <label for="nic">NIC</label>
                                    <input type="text" name="representative[nic]" id="nic" class="form-control"
                                        value="{{ old('representative.nic', $membership->representative?->nic) }}">
                                </div>

                                <div class="form-group">
                                    <label for="phone_number">Phone Number</label>
                                    <input type="text" name="representative[phone_number]" id="phone_number"
                                        class="form-control"
                                        value="{{ old('representative.phone_number', $membership->representative?->phone_number) }}">
                                </div>

                                <div class="form-group">
                                    <label for="relationship">Relationship</label>
                                    <select name="representative[relationship]" class="form-control">
                                        <option></option>
                                        @foreach(\App\Enums\RelationshipType::cases() as $case)
                                        <option value="{{ $case->value }}"
                                            {{ $membership->representative?->relationship === $case->value ? 'selected' : '' }}>
                                            {{ $case->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="representative_signature">Representative Signature</label>
                                    <input type="file" class="form-control" name="membership[representative_signature]"
                                        id="representative_signature" accept="image/*"
                                        onchange="previewImage(event, 'representativeSignaturePreview')">
                                    <img id="representativeSignaturePreview" src="#"
                                        alt="Representative Signature Preview"
                                        style="display:none; width:200px; margin-top:10px;">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Update Membership
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
$(document).ready(function() {
  $('.select2').select2();
});

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