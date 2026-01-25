@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Weighbridge Entry')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')

@php
$manualEnabled = \App\Models\Setting::get('weighbridge_manual_enable', 0);
@endphp
<style>
/* Make focused inputs more visible */
.form-control:focus {
    border-color: #ff6600 !important;
    /* Bright orange border */
    box-shadow: 0 0 5px 2px rgba(255, 102, 0, 0.5) !important;
    /* Glowing outline */
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
                    <h3 class="card-title">Add 2nd weight entry# {{$data->id}}</h3>
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
                    <div class="row">
                        <div class="col-md-6">
                            <form id="weighbridge_form" action="{{ route('weighbridge.final.update', $data->id) }}"
                                method="POST" autocomplete="off">
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
                                            <option value="Ag Salt" {{ $data->culture == 'Ag Salt' ? 'selected' : '' }}>
                                                Ag Salt</option>
                                            <option value="yala" {{ $data->culture == 'yala' ? 'selected' : '' }}>Yala
                                            </option>
                                            <option value="maha" {{ $data->culture == 'maha' ? 'selected' : '' }}>Maha
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="side_id" class="col-sm-3 col-form-label">Side</label>
                                    <div class="col-sm-9">
                                        <select id="side_id" name="side_id" class="form-control" required tabindex="2">
                                            <option value="">-- Select Side --</option>
                                            @foreach ($sides as $side)
                                            <option value="{{ $side->id }}"
                                                {{ $data->membership->saltern->yahai->side_id == $side->id ? 'selected' : '' }}>
                                                {{ ucfirst($side->name) }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="yahai_id" class="col-sm-3 col-form-label">Yahai</label>
                                    <div class="col-sm-9">
                                        <select id="yahai_id" name="yahai_id" class="form-control" required
                                            tabindex="3">
                                            <option value="">-- Select Yahai --</option>
                                            @foreach ($yahais as $yahai)
                                            <option value="{{ $yahai->id }}"
                                                {{ $data->membership->saltern->yahai_id == $yahai->id ? 'selected' : '' }}>
                                                {{ $yahai->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="saltern_id" class="col-sm-3 col-form-label">Waikal No</label>
                                    <div class="col-sm-9">
                                        <select id="saltern_id" name="saltern_id" class="form-control" required
                                            tabindex="4">
                                            <option value="">-- Select Waikal No --</option>
                                            @foreach ($salterns as $saltern)
                                            <option value="{{ $saltern->id }}"
                                                {{ $data->membership->saltern_id == $saltern->id ? 'selected' : '' }}>
                                                {{ $saltern->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="owner_full_name" class="col-sm-3 col-form-label">Owner</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="membership_name" id="membership_name"
                                            class="form-control" required readonly
                                            value="{{ $data->membership->owner->name_with_initial}}">
                                        <input type="hidden" name="membership_id" id="membership_id"
                                            class="form-control" required value="{{$data->membership_id}}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="buyer_id" class="col-sm-3 col-form-label">Buyer</label>
                                    <div class="col-sm-9">
                                        <select name="buyer_id" id="buyer_id" class="form-control" required
                                            tabindex="5">
                                            <option value="">Select Buyer</option>
                                            @foreach($buyers as $buyer)
                                            <option value="{{ $buyer->id }}"
                                                {{ $data->buyer_id == $buyer->id ? 'selected' : '' }}>
                                                {{ $buyer->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="vehicle_id" class="col-sm-3 col-form-label">Vehicle ID</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="vehicle_id" id="vehicle_id" class="form-control"
                                            required tabindex="6" value="{{ $data->vehicle_id }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="initial_weight" class="col-sm-3 col-form-label">1st Weight</label>
                                    <div class="col-sm-9">
                                        <input type="number" step="1" name="initial_weight" id="initial_weight"
                                            class="form-control" required tabindex="7"
                                            value="{{ $data->initial_weight }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="tare_weight" class="col-sm-3 col-form-label">2nd Weight</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input type="text" name="tare_weight" id="tare_weight"
                                                class="form-control" required tabindex="8">

                                            <button type="button" id="clear_weight" class="btn btn-sm  btn-outline-secondary">
                                                Clear
                                            </button>
                                        </div>
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
<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("#weighbridge_form");
    const initialWeightInput = document.getElementById('initial_weight'); // readonly
    const tareWeightInput = document.getElementById('tare_weight'); // 2nd weight
    const netWeightInput = document.getElementById('net_weight');
    const serviceChargeInput = document.getElementById('service_charge');
    const bagsInput = document.getElementById('bags');
    const descriptionText = document.getElementById('description');
    const submitBtn = form.querySelector('button[type="submit"]');
    const clearBtn = document.getElementById('clear_weight');

    const SERVICE_CHARGE_RATE = 100; // adjust as needed

    // ---------------------
    // 2nd Weight Calculation
    // ---------------------
    function calculateValues() {
        const initialWeight = parseFloat(initialWeightInput.value) || 0;
        const tareWeight = parseFloat(tareWeightInput.value) || 0;

        if (initialWeight > 0 && tareWeight > 0 && tareWeight >= initialWeight) {
            const netWeight = tareWeight - initialWeight;
            const bags = netWeight / 50;
            const serviceCharge = bags * SERVICE_CHARGE_RATE;

            netWeightInput.value = netWeight;
            serviceChargeInput.value = serviceCharge.toFixed(2);
            bagsInput.value = bags.toFixed(2);

            const debitAmount = serviceCharge * 0.30;
            const creditAmount = serviceCharge * 0.70;

            descriptionText.innerHTML = `
                Owner's Account: Debit <strong>30%</strong> = <strong>LKR ${debitAmount.toFixed(2)}</strong><br>
                Service Charge Account: Credit <strong>70%</strong> = <strong>LKR ${creditAmount.toFixed(2)}</strong>
            `;
        } else {
            netWeightInput.value = '';
            serviceChargeInput.value = '';
            bagsInput.value = '';
            descriptionText.innerHTML = '';
        }
    }

    tareWeightInput.addEventListener('input', calculateValues);

    // ---------------------
    // Clear Button
    // ---------------------
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            tareWeightInput.value = '';
            netWeightInput.value = '';
            serviceChargeInput.value = '';
            bagsInput.value = '';
            descriptionText.innerHTML = '';
            tareWeightInput.focus();
        });
    }

    // ---------------------
    // Form Submit Validation
    // ---------------------
    form.addEventListener('submit', function(e) {
        if (tareWeightInput.value.trim() === '') {
            e.preventDefault();
            alert('Please enter 2nd weight!');
            tareWeightInput.focus();
            return false;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    });

    // ---------------------
    // Tab key navigation
    // ---------------------
    form.addEventListener("keydown", function(e) {
        if (e.key === "Tab") {
            const focusable = Array.from(form.querySelectorAll("input, select, textarea, button"))
                .filter(el => !el.disabled && el.type !== "hidden" && el.offsetParent !== null && !el
                    .readOnly);

            const index = focusable.indexOf(document.activeElement);
            if (index === -1) return;

            e.preventDefault();
            const nextIndex = e.shiftKey ? (index - 1 + focusable.length) % focusable.length : (index +
                1) % focusable.length;
            focusable[nextIndex].focus();
        }
    });

    // ---------------------
    // Dropdown AJAX: Side -> Yahai -> Saltern -> Membership
    // ---------------------
    function clearMembershipDetails() {
        $('#membership_id').val('');
        $('#membership_name').val('');
    }

    $('#side_id').change(function() {
        const sideId = $(this).val();
        clearMembershipDetails();

        $('#yahai_id').prop('disabled', true).empty().append('<option value="">Select Yahai</option>');
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
                }
            });
        }
    });

    $('#yahai_id').change(function() {
        const yahaiId = $(this).val();
        clearMembershipDetails();
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
                }
            });
        }
    });

    $('#saltern_id').change(function() {
        const salternId = $(this).val();
        clearMembershipDetails();

        if (salternId) {
            // Membership
            $.ajax({
                url: "{{ route('get.membership', '') }}/" + salternId,
                type: "GET",
                success: function(response) {
                    if (response.status === "success") {
                        $('#membership_id').val(response.membership.id);
                        $('#membership_name').val(response.owner.name_with_initial);
                    } else {
                        alert('No membership found for this saltern');
                    }
                }
            });

            // Saltern loan details
            $.ajax({
                url: "{{ route('get.saltern.details', '') }}/" + salternId,
                type: "GET",
                success: function(data) {
                    $('#saltern_details').html(data);
                },
                error: function() {
                    $('#saltern_details').html(
                        '<p>An error occurred. Please try again.</p>');
                }
            });
        }
    });

    // ---------------------
    // Load data on page load if editing
    // ---------------------
    let salternId = "{{ $data->membership->saltern_id }}";
    if (salternId) {
        // Load membership and loans
        $.ajax({
            url: "{{ route('get.membership', '') }}/" + salternId,
            type: "GET",
            success: function(response) {
                if (response.status === "success") {
                    $('#membership_id').val(response.membership.id);
                    $('#membership_name').val(response.owner.name_with_initial);
                }
            }
        });

        $.ajax({
            url: "{{ route('get.saltern.details', '') }}/" + salternId,
            type: "GET",
            success: function(data) {
                $('#saltern_details').html(data);
            }
        });
    }

});
</script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const weightInput = document.getElementById("tare_weight");

    // Laravel → JS flag
    const manualEnabled = {{ $manualEnabled ? 'true' : 'false' }};

    let filledByWeighbridge = false;

    /* ❌ Block paste only if manual disabled */
    weightInput.addEventListener("paste", e => {
        if (!manualEnabled) {
            e.preventDefault();
            alert("Paste is disabled. Use weighbridge (F8).");
        }
    });

    /* ❌ Detect manual typing only if manual disabled */
    weightInput.addEventListener("input", () => {
        if (!manualEnabled && !filledByWeighbridge) {
            weightInput.value = "";
        }
    });

    /* 🎯 F8 prepares field */
    document.addEventListener("keydown", e => {
        if (e.key === "F8") {
            e.preventDefault();

            filledByWeighbridge = true;
            weightInput.focus();
            weightInput.value = ""; // python types next

            // reset AFTER python typing
            setTimeout(() => {
                filledByWeighbridge = false;
            }, 400);
        }
    });
});
</script>
@endpush