@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Private Weighbridge Entry')
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
                    <h3 class="card-title">Create First Weight # {{$nextSerialNo}}</h3>
                </div>
                <div class="card-body">
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
                            <form id="weighbridge_form" action="{{ route('private-weighbridge-entries.store') }}"
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
                                    <label for="customer_name" class="col-sm-3 col-form-label">Customer Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="customer_name" id="customer_name"
                                            class="form-control" value="">
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
                                    <label for="first_weight" class="col-sm-3 col-form-label">1st Weight</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input type="text" name="first_weight" id="first_weight"
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
    const initialWeight = document.getElementById('first_weight');
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
});
</script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const weightInput = document.getElementById("first_weight");

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