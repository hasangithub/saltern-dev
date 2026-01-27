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
                    <h3 class="card-title">Add 2nd weight entry# {{$privateWeighbridgeEntry->id}}</h3>
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
                            <form id="weighbridge_form"
                                action="{{ route('private-weighbridge-entries.update', $privateWeighbridgeEntry) }}"
                                method="POST" autocomplete="off">
                                @csrf
                                @method('PUT')
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
                                        <input type="text" name="customer_name" id="customer_name" class="form-control"
                                            value="{{ $privateWeighbridgeEntry->customer_name }}">
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
                                                {{ $privateWeighbridgeEntry->buyer_id == $buyer->id ? 'selected' : '' }}>
                                                {{ $buyer->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="vehicle_id" class="col-sm-3 col-form-label">Vehicle ID</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="vehicle_id" id="vehicle_id" class="form-control"
                                            required tabindex="6" value="{{ $privateWeighbridgeEntry->vehicle_id }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="first_weight" class="col-sm-3 col-form-label">1st Weight</label>
                                    <div class="col-sm-9">
                                        <input type="number" step="1" name="first_weight" id="first_weight"
                                            class="form-control" required tabindex="7"
                                            value="{{ $privateWeighbridgeEntry->first_weight }}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="second_weight" class="col-sm-3 col-form-label">2nd Weight</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input type="text" name="second_weight" id="second_weight"
                                                class="form-control" tabindex="8">

                                            <button type="button" id="clear_weight"
                                                class="btn btn-sm  btn-outline-secondary">
                                                Clear
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="amount" class="col-sm-3 col-form-label">Amount</label>
                                    <div class="col-sm-9">
                                        <input type="number" step="0.01" name="amount" id="amount" class="form-control"
                                            value="">
                                    </div>
                                </div>

                                <div class="form-check mb-3">
                                    <input type="checkbox" name="is_paid" value="1" class="form-check-input"
                                        id="is_paid" {{ $privateWeighbridgeEntry->is_paid ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_paid">Cash Paid</label>
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
    const initialWeightInput = document.getElementById('first_weight'); // readonly
    const tareWeightInput = document.getElementById('second_weight'); // 2nd weight
    const submitBtn = form.querySelector('button[type="submit"]');
    const clearBtn = document.getElementById('clear_weight');

    // ---------------------
    // Clear Button
    // ---------------------
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            tareWeightInput.value = '';
            tareWeightInput.focus();
        });
    }

    // ---------------------
    // Form Submit Validation
    // ---------------------
    form.addEventListener('submit', function(e) {
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
});
</script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const weightInput = document.getElementById("second_weight");

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