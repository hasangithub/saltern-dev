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
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <form method="POST" action="{{ route('memberships.store') }}" enctype="multipart/form-data"
                        autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="side_id" class="form-label">Select Side</label>
                                    <select id="side_id" name="side_id" class="form-control" required>
                                        <option value="">-- Select Side --</option>
                                        @foreach ($sides as $side)
                                        <option value="{{ $side->id }}">{{ ucfirst($side->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="yahai_id" class="form-label">Select Yahai</label>
                                    <select id="yahai_id" name="yahai_id" class="form-control select2" required>
                                        <option value="">-- Select Yahai --</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="saltern_id" class="form-label">Select Saltern</label>
                                    <select id="saltern_id" name="saltern_id" class="form-control select2" required>
                                        <option value="">-- Select Saltern --</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="owner_id">Owner</label>
                                    <select class="form-control select2" name="owner_id" id="owner_id" required>
                                        <option value="">Select Owner</option>
                                        @foreach($owners as $owner)
                                        <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="membership_no">Membership No</label>
                                    <input type="text" class="form-control" name="membership_no" id="membership_no"
                                        required>
                                </div>

                                <div class="form-group">
                                    <label for="membership_date">Membership Date</label>
                                    <input type="date" class="form-control" name="membership_date" id="membership_date"
                                        required>
                                </div>

                                <div class="form-group">
                                    <label for="owner_signature">Owner Signature</label>
                                    <input type="file" class="form-control" name="owner_signature" id="owner_signature"
                                        accept="image/*" 
                                        onchange="previewImage(event, 'ownerSignaturePreview')">
                                    <img id="ownerSignaturePreview" src="#" alt="Owner Signature Preview"
                                        style="display:none; width:200px; margin-top:10px;">
                                </div>

                                <div class="form-group form-check">
                                    <input type="checkbox" class="form-check-input" name="is_active" id="is_active"
                                        value="1">
                                    <label class="form-check-label" for="is_active">Active Membership</label>
                                </div>
                            </div>
                        </div>
                        <h3>Representative Details</h3>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="full_name">Name with Initial</label>
                                    <input type="text" name="name_with_initial" id="name_with_initial"
                                        class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="nic">NIC</label>
                                    <input type="text" name="nic" id="nic" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="phone_number">Phone Number</label>
                                    <input type="text" name="phone_number" id="phone_number" class="form-control"
                                        value="{{ old('phone_number') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="relationship">Relationship</label>
                                    <select name="relationship" class="form-control" required>
                                        <option value=""></option>
                                        @foreach($genders as $gender)
                                        <option value="{{ $gender->value }}">{{ $gender->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="representative_signature">Representative Signature</label>
                                    <input type="file" class="form-control" name="representative_signature"
                                        id="representative_signature" accept="image/*"
                                        onchange="previewImage(event, 'representativeSignaturePreview')">
                                    <img id="representativeSignaturePreview" src="#"
                                        alt="Representative Signature Preview"
                                        style="display:none; width:200px; margin-top:10px;">
                                </div>
                            </div>
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
$(document).ready(function() {
    $('.select2').select2();
    $('#side_id').change(function() {
        const sideId = $(this).val();

        // Reset and disable the Yahai dropdown
        $('#yahai_id').prop('disabled', true).empty().append('<option value="">Select Yahai</option>');

        // Reset and disable the Saltern dropdown
        $('#saltern_id').prop('disabled', true).empty().append('<option value="">Select Saltern</option>');

        if (sideId) {
            $.ajax({
                url: "{{ route('get.yahai') }}",
                type: "GET",
                data: {
                    side_id: sideId
                },
                success: function(response) {
                    response.yahais.forEach(yahai => {
                        $('#yahai_id').append(
                            `<option value="${yahai.id}">${yahai.name}</option>`
                        );
                    });
                    $('#yahai_id').prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching Yahais:', error);
                }
            });
        }
    });

    $('#yahai_id').change(function() {
        const yahaiId = $(this).val();
        $('#saltern_id').prop('disabled', true).empty().append(
            '<option value="">Select Saltern</option>');
        if (yahaiId) {
            $.ajax({
                url: "{{ route('get.saltern') }}",
                type: "GET",
                data: {
                    yahai_id: yahaiId
                },
                success: function(response) {
                    response.salterns.forEach(saltern => {
                        $('#saltern_id').append(
                            `<option value="${saltern.id}">${saltern.name}</option>`
                        );
                    });
                    $('#saltern_id').prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching salterns :', error);
                }
            });
        }
    });
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