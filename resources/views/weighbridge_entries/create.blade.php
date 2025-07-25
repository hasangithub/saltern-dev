@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Weighbridge Entry')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')

<style>

/* Make focused inputs more visible */
.form-control:focus {
    border-color: #ff6600 !important;    /* Bright orange border */
    box-shadow: 0 0 5px 2px rgba(255, 102, 0, 0.5) !important; /* Glowing outline */
    outline: none !important;
}

/* Optional: style selects and textareas too */
select.form-control:focus,
textarea.form-control:focus {
    border-color: #ff6600 !important;
    box-shadow: 0 0 5px 2px rgba(255, 102, 0, 0.5) !important;
    outline: none !important;
}
</style>


<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Create new entry#  {{$nextSerialNo}}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <form id="weighbridge_form" action="{{ route('weighbridge_entries.store') }}" method="POST" autocomplete="off">
                                @csrf
                                <div class="form-group row">
                                    <label for="transaction_date" class="col-sm-3 col-form-label">Date</label>
                                    <div class="col-sm-9">
                                        <input type="date" name="transaction_date" id="transaction_date"
                                            class="form-control" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="culture" class="col-sm-3 col-form-label">Culture</label>
                                    <div class="col-sm-9">
                                        <select id="culture" name="culture" class="form-control" required tabindex="1">
                                            <option value="">-- Select culture --</option>
                                            <option value="Ag Salt">Ag Salt</option>
                                            <option value="yala">Yala</option>
                                            <option value="maha">Maha</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="side_id" class="col-sm-3 col-form-label">Side</label>
                                    <div class="col-sm-9">
                                        <select id="side_id" name="side_id" class="form-control" required tabindex="2">
                                            <option value="">-- Select Side --</option>
                                            @foreach ($sides as $side)
                                            <option value="{{ $side->id }}">{{ ucfirst($side->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="yahai_id" class="col-sm-3 col-form-label">Yahai</label>
                                    <div class="col-sm-9">
                                        <select id="yahai_id" name="yahai_id" class="form-control" required tabindex="3">
                                            <option value="">-- Select Yahai --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="saltern_id" class="col-sm-3 col-form-label">Waikal No</label>
                                    <div class="col-sm-9">
                                        <select id="saltern_id" name="saltern_id" class="form-control" required tabindex="4">
                                            <option value="">-- Select Waikal No --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="owner_full_name" class="col-sm-3 col-form-label">Owner</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="membership_name" id="membership_name"
                                            class="form-control" required readonly>
                                        <input type="hidden" name="membership_id" id="membership_id"
                                            class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="buyer_id" class="col-sm-3 col-form-label">Buyer</label>
                                    <div class="col-sm-9">
                                        <select name="buyer_id" id="buyer_id" class="form-control" required tabindex="5">
                                            <option value="">Select Buyer</option>
                                            @foreach($buyers as $buyer)
                                            <option value="{{ $buyer->id }}">{{ $buyer->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="vehicle_id" class="col-sm-3 col-form-label">Vehicle ID</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="vehicle_id" id="vehicle_id" class="form-control"
                                            required tabindex="6">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="initial_weight" class="col-sm-3 col-form-label">1st Weight</label>
                                    <div class="col-sm-9">
                                        <input type="number" step="1" name="initial_weight" id="initial_weight"
                                            class="form-control" required tabindex="7">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="initial_weight" class="col-sm-3 col-form-label">2nd Weight</label>
                                    <div class="col-sm-9">
                                        <input type="number" step="1" name="tare_weight" id="tare_weight"
                                            class="form-control" required tabindex="8">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="card card-default">
                                        <div class="card-header">
                                            <h3 class="card-title">Loan Details</h3>
                                        </div>

                                        <div class="card-body">
                                            <div id="saltern_details" class="mt-4">

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="initial_weight" class="col-sm-3 col-form-label">Net Weight</label>
                                <div class="col-sm-9">
                                    <input type="number" step="1" name="net_weight" id="net_weight" class="form-control"
                                        readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="bags" class="col-sm-3 col-form-label">Bags</label>
                                <div class="col-sm-9">
                                    <input type="number" step="0.01" name="bags" id="bags" class="form-control"
                                        readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="service_charge" class="col-sm-3 col-form-label">Service Charge</label>
                                <div class="col-sm-9">
                                    <input type="number" step="0.01" name="service_charge" id="service_charge"
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card mt-3">
                                        <div class="card-body bg-light">
                                            <h5 class="card-title text-primary">Transaction Details</h5>
                                            <p id="description" class="card-text text-muted">
                                                The details of the transaction will be displayed here.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (confirm("{{ session('success') }}\n\nDo you want to print the invoice?")) {
                window.open("{{ route('weighbridge_entries.invoice', session('print_entry_id')) }}", "_blank");
            }
        });
    </script>
@endif
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("#weighbridge_form"); // Change as needed

    form.addEventListener("keydown", function (e) {
        if (e.key === "Tab") {
            const focusable = Array.from(form.querySelectorAll("input, select, textarea, button"))
                .filter(el =>
                    !el.disabled &&
                    el.type !== "hidden" &&
                    el.offsetParent !== null && // visible
                    !el.readOnly // skip readonly
                );

            const index = focusable.indexOf(document.activeElement);
            if (index === -1) return; // if not in list

            e.preventDefault();

            const nextIndex = e.shiftKey
                ? (index - 1 + focusable.length) % focusable.length
                : (index + 1) % focusable.length;

            focusable[nextIndex].focus();
        }
    });
});
$(document).ready(function() {
    const $form = $('form');
    const $submitBtn = $form.find('button[type="submit"]');

    // Re-enable submit button on page load in case it was disabled before
    if ($submitBtn.prop('disabled')) {
        $submitBtn.prop('disabled', false);
        $submitBtn.text('Save'); // Reset to your default button text
    }

    $form.on('submit', function() {
        $submitBtn.prop('disabled', true);
        $submitBtn.text('Submitting...');
    });

    $('#side_id').change(function() {
        const sideId = $(this).val();

        clearMembershipDetails();

        // Reset and disable the Yahai dropdown
        $('#yahai_id').prop('disabled', true).empty().append('<option value="">Select Yahai</option>');

        // Reset and disable the Saltern dropdown
        $('#saltern_id').prop('disabled', true).empty().append(
            '<option value="">Select Saltern</option>');

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
        clearMembershipDetails();
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

    $('#saltern_id').change(function() {
        const salternId = $(this).val(); // Get selected saltern ID

        clearMembershipDetails();

        if (salternId) {
            $.ajax({
                url: "{{ route('get.membership', '') }}/" + salternId,
                type: "GET",
                success: function(response) {
                    if (response.status === 'success') {
                        const membership = response.membership;
                        const owner = response.owner;
                        // Populate the form with membership details
                        $('#membership_id').val(membership.id);
                        $('#membership_name').val(owner.name_with_initial);
                    } else {
                        alert('No membership found for this saltern');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching membership details:', error);
                }
            });

            $.ajax({
                url: "{{ route('get.saltern.details', '') }}/" + salternId,
                type: "GET",
                success: function(loans) {
                    $('#saltern_details').html(loans);
                },
                error: function() {
                    $('#saltern_details').html(
                        '<p>An error occurred. Please try again.</p>');
                },
            });
        }
    });

    function clearMembershipDetails() {
        $('#membership_name').val('');
        $('#membership_name').val('');
        $('#membership_name').val('');
        $('#membership_name').val('');
        $('#membership_name').val('');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const initialWeightInput = document.getElementById('initial_weight');
    const tareWeightInput = document.getElementById('tare_weight');
    const netWeightInput = document.getElementById('net_weight');
    const serviceChargeInput = document.getElementById('service_charge');
    const bagsInput = document.getElementById('bags');
    const descriptionText = document.getElementById('description');

    const SERVICE_CHARGE_RATE = 100; // Example service charge per kg (adjust as needed)

    function calculateValues() {
        const initialWeight = parseFloat(initialWeightInput.value) || 0;
        const tareWeight = parseFloat(tareWeightInput.value) || 0;

        if (initialWeight > 0 && tareWeight > 0 && tareWeight >= initialWeight) {
            const netWeight = tareWeight - initialWeight;
            const bags = netWeight / 50;
            const serviceCharge = bags * SERVICE_CHARGE_RATE;


            netWeightInput.value = netWeight; // Display net weight
            serviceChargeInput.value = serviceCharge.toFixed(2); // Display service charge
            bagsInput.value = bags.toFixed(2);

            const debitAmount = serviceCharge * 0.30;
            const creditAmount = serviceCharge * 0.70;

            // Update the description field
            descriptionText.innerHTML = `<br>
                Owner's Account: Debit <strong> 30% </strong> of Service Charge = <strong>LKR ${debitAmount.toFixed(2)} </strong><br>
                Service Charge Account: Credit <strong> 70% </strong> of Service Charge = <strong> LKR ${creditAmount.toFixed(2)} </strong>
            `.trim();

        } else {
            netWeightInput.value = ''; // Clear fields if invalid input
            serviceChargeInput.value = '';
            bagsInput.value = '';
            descriptionText.innerHTML = '';
        }
    }

    // Add event listeners to update values dynamically
    initialWeightInput.addEventListener('input', calculateValues);
    tareWeightInput.addEventListener('input', calculateValues);
});
</script>
@endpush