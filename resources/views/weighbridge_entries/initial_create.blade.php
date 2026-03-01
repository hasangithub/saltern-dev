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
                <div class="card-header border-left border-warning">
                    <h6 class="mb-0">
                        <i class="fa fa-balance-scale text-warning"></i>
                        Add First Weight
                        <small class="text-muted">| Entry #{{ $nextSerialNo }}</small>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <form id="weighbridge_form" action="{{ route('weighbridge.initial.store') }}" method="POST"
                                autocomplete="off">
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
                                        <select id="yahai_id" name="yahai_id" class="form-control" required
                                            tabindex="3">
                                            <option value="">-- Select Yahai --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="saltern_id" class="col-sm-3 col-form-label">Waikal No</label>
                                    <div class="col-sm-9">
                                        <select id="saltern_id" name="saltern_id" class="form-control" required
                                            tabindex="4">
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
                                    <label for="membership_phone_number" class="col-sm-3 col-form-label">Owner Phone Number</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="membership_phone_number" id="membership_phone_number"
                                            class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="buyer_id" class="col-sm-3 col-form-label">Buyer</label>
                                    <div class="col-sm-9">
                                        <select name="buyer_id" id="buyer_id" class="form-control" required
                                            tabindex="5">
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
                                        <div class="input-group">
                                            <input type="text" name="initial_weight" id="initial_weight"
                                                class="form-control" required tabindex="7" value="">

                                            <button type="button" id="clear_weight"
                                                class="btn btn-sm btn-secondary">Clear</button>
                                        </div>
                                    </div>
                                </div>


                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <div class="row justify-content-end">

                                <!-- Weighbridge Pending -->
                                <div class="col-md-4 col-sm-6 mb-2">
                                    <div class="card border-warning shadow-sm">
                                        <div class="card-body p-4 text-center">
                                            <small class="text-danger font-weight-bold">
                                                <i class="fa fa-truck"></i> Weighbridge Pending
                                            </small>

                                            <h5 class="mb-0 text-dark">
                                                {{ $pendingCount }}
                                            </h5>
                                        </div>
                                    </div>
                                </div>

                                <!-- Private Weigh Pending -->
                                <div class="col-md-4 col-sm-6 mb-2">
                                    <div class="card border-warning shadow-sm">
                                        <div class="card-body p-4 text-center">
                                            <small class="text-danger font-weight-bold">
                                                <i class="fa fa-balance-scale"></i> Private Weigh Pending
                                            </small>

                                            <h5 class="mb-0 text-dark">
                                                {{ $privateWeighPendingCount }}
                                            </h5>
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
    const initialWeight = document.getElementById('initial_weight');
    const submitBtn = form.querySelector('button[type="submit"]');

    const clearBtn = document.getElementById('clear_weight');
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            initialWeight.value = ''; // Reset value
            initialWeight.focus(); // Focus back to input
        });
    }

    // ---------------------
    // Submit event with validation
    // ---------------------
    form.addEventListener('submit', function(e) {
        const weightVal = initialWeight.value.trim();

        // Stop submit if empty
        if (weightVal === '') {
            e.preventDefault();
            alert('First weight cannot be empty!');
            initialWeight.focus();
            return false;
        }

        // Disable button to prevent double submit
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    });

    // ---------------------
    // Tab key navigation skipping readonly/disabled
    // ---------------------
    form.addEventListener("keydown", function(e) {
        if (e.key === "Tab") {
            const focusable = Array.from(form.querySelectorAll("input, select, textarea, button"))
                .filter(el =>
                    !el.disabled &&
                    el.type !== "hidden" &&
                    el.offsetParent !== null &&
                    !el.readOnly
                );

            const index = focusable.indexOf(document.activeElement);
            if (index === -1) return;

            e.preventDefault();
            const nextIndex = e.shiftKey ?
                (index - 1 + focusable.length) % focusable.length :
                (index + 1) % focusable.length;
            focusable[nextIndex].focus();
        }
    });

    // ---------------------
    // Dropdown AJAX handling
    // ---------------------
    function clearMembershipDetails() {
        $('#membership_id').val('');
        $('#membership_name').val('');
        $('#membership_phone_number').val('');
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
                }
            });
        }
    });

    $('#saltern_id').change(function() {
        const salternId = $(this).val();
        clearMembershipDetails();

        if (salternId) {
            $.ajax({
                url: "{{ route('get.membership', '') }}/" + salternId,
                type: "GET",
                success: function(response) {
                    if (response.status === 'success') {
                        $('#membership_id').val(response.membership.id);
                        $('#membership_name').val(response.owner.name_with_initial);
                        $('#membership_phone_number').val(response.owner.phone_number);
                    } else {
                        alert('No membership found for this saltern');
                    }
                }
            });
        }
    });

    // ---------------------
    // Success session alert & print
    // ---------------------
    @if(session('success'))
    @if(session('print_type') === 'second')
    if (confirm("{{ session('success') }}\n\nDo you want to print the invoice?")) {
        window.open("{{ route('weighbridge_entries.invoice', session('print_entry_id')) }}",
            "_blank");
    }
    @else
    if (confirm("{{ session('success') }}\n\nDo you want to print the invoice?")) {
        window.open("{{ route('weighbridge_entries.invoicePrintFirst', session('print_entry_id')) }}",
            "_blank");
    }
    @endif

    @endif
});
</script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const weightInput = document.getElementById("initial_weight");

    // Laravel → JS flag
    const manualEnabled = {
        {
            $manualEnabled ? 'true' : 'false'
        }
    };

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