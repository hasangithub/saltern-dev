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
                <div class="card-header">
                    <h3 class="card-title" id="card-title"></h3>
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
                    <form id="wizard-form" method="POST" action="">
                        @csrf

                        <!-- Step 1: Select Yahai and Saltern -->
                        <div class="wizard-step" id="step-1">
                            <div class="form-group">
                                <label for="yahai">Yahai</label>
                                <select name="yahai_id" id="yahai" class="form-control" required>
                                    <option value="">Select Yahai</option>
                                    @foreach($yahais as $yahai)
                                    <option value="{{ $yahai->id }}">{{ $yahai->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="saltern">Saltern</label>
                                <select name="saltern_id" id="saltern" class="form-control">
                                    <option value="">Select Saltern</option>
                                </select>
                            </div>
                        </div>

                        <!-- Step 2: Select or Create Owner -->
                        <div class="wizard-step" id="step-2" style="display: none;">
                            <div class="form-group">
                                <div>
                                    <label class="radio-inline">
                                        <input type="radio" name="owner_type" value="existing" id="existing-owner"
                                            checked> Existing Owner
                                    </label>
                                    <br>
                                    <label class="radio-inline">
                                        <input type="radio" name="owner_type" value="new" id="new-owner"> New Owner
                                    </label>
                                </div>
                            </div>

                            <!-- Existing Owner Dropdown -->
                            <div class="form-group" id="existing-owner-section">
                                <label for="owner">Select Existing Owner</label>
                                <select name="owner_id" id="owner" class="form-control">
                                    <option value="">Select Owner</option>
                                    @foreach($owners as $owner)
                                    <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- New Owner Input Fields -->
                            <div id="new-owner-section" style="display: none;">
                                <div class="form-group">
                                    <label for="new_owner_name">Owner Name</label>
                                    <input type="text" name="new_owner_name" id="new_owner_name" class="form-control"
                                        placeholder="Enter Owner Name">
                                </div>
                                <div class="form-group">
                                    <label for="new_owner_email">Owner Email</label>
                                    <input type="email" name="new_owner_email" id="new_owner_email" class="form-control"
                                        placeholder="Enter Owner Email">
                                </div>
                                <div class="form-group">
                                    <label for="new_owner_phone">Owner Phone</label>
                                    <input type="text" name="new_owner_phone" id="new_owner_phone" class="form-control"
                                        placeholder="Enter Owner Phone">
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Create Membership Details -->
                        <div class="wizard-step" id="step-3" style="display: none;">
                            <div class="form-group">
                                <label for="membership_date">Membership Date</label>
                                <input type="date" name="membership_date" id="membership_date" class="form-control"
                                    required>
                            </div>
                        </div>

                        <div class="wizard-step" id="step-4" style="display: none;">
                            <div class="form-group">
                                <label for="membership_date">Name</label>
                                <input type="text" name="membership_date" id="membership_date" class="form-control"
                                    required>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <div class="wizard-navigation">
                            <button type="button" class="btn btn-secondary" id="prev-step"
                                style="display: none;">Previous</button>
                            <button type="button" class="btn btn-primary" id="next-step">Next</button>
                            <button type="submit" class="btn btn-success" id="submit-form"
                                style="display: none;">Submit</button>
                        </div>
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
<script>
$(document).ready(function() {
    let currentStep = 1;
    const totalSteps = $('.wizard-step').length;
    const stepTitles = [
        "Step 1: Select Saltern",
        "Step 2: Select Owner or Create New Owner",
        "Step 3: Membership Details",
        "Step 4: Represntative  Details"
    ];

    function updateCardTitle(step) {
        $('#card-title').text(stepTitles[step-1]);
    }

    function showStep(step) {
        updateCardTitle(step);
        $('.wizard-step').hide();
        $(`#step-${step}`).show();

        $('#prev-step').toggle(step > 1);
        $('#next-step').toggle(step < totalSteps);
        $('#submit-form').toggle(step === totalSteps);
    }


    $('#next-step').click(function() {
        if (currentStep < totalSteps) {
            currentStep++;
            showStep(currentStep);
        }
    });

    $('#prev-step').click(function() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });

    // Fetch Salterns based on Yahai selection

    $(document).ready(function() {
        $('#yahai').change(function() {
            const yahaiId = $(this).val();
            if (yahaiId) {
                const fetchSubledgersUrl = "{{ route('api.salterns', ':yahaiId') }}";
                const url = fetchSubledgersUrl.replace(':yahaiId', yahaiId);
                // Clear and disable the Saltern dropdown while loading
                $('#saltern').empty().append('<option value="">Loading...</option>').prop(
                    'disabled', true);

                // AJAX request to fetch relevant Salterns
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(data) {
                        $('#saltern').empty().append(
                            '<option value="">Select Saltern</option>');
                        data.forEach(saltern => {
                            $('#saltern').append(
                                `<option value="${saltern.id}">${saltern.name}</option>`
                            );
                        });
                        $('#saltern').prop('disabled',
                            false); // Enable the dropdown
                    },
                    error: function() {
                        alert('Error loading Salterns');
                        $('#saltern').empty().append(
                                '<option value="">Select Saltern</option>')
                            .prop('disabled', false);
                    }
                });
            } else {
                // Reset Saltern dropdown if no Yahai is selected
                $('#saltern').empty().append('<option value="">Select Saltern</option>').prop(
                    'disabled',
                    true);
            }
        });
    });

    showStep(currentStep);

    checkOwnerType();

    // Check again when the user changes the radio selection
    $('input[name="owner_type"]').change(function() {
        checkOwnerType();
    });
});

function checkOwnerType() {
    const ownerType = $('input[name="owner_type"]:checked').val();
    if (ownerType === 'existing') {
        $('#existing-owner-section').show();
        $('#new-owner-section').hide();
    } else if (ownerType === 'new') {
        $('#existing-owner-section').hide();
        $('#new-owner-section').show();
    }
}
</script>
@endpush