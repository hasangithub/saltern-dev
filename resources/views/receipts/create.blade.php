@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Receipts')
@section('content_header_subtitle', 'Receipts')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Buyer</h3>
        </div>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif
        <div class="card-body">
            <form method="GET" action="{{ route('receipts.create') }}">
                <label>Select Buyer:</label>
                <select name="buyer_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Select Buyer --</option>
                    @foreach($buyers as $buyer)
                    <option value="{{ $buyer->id }}" {{ request('buyer_id') == $buyer->id ? 'selected' : '' }}>
                        {{ $buyer->full_name }}
                    </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Pendings</h3>
            <strong>Total Selected Amount: <span id="totalAmount">0.00</span></strong>
        </div>

        <div class="card-body">
            @if(request('buyer_id'))
            <form method="POST" action="{{ route('receipts.store') }}">
                @csrf
                <input type="hidden" name="buyer_id" value="{{ $buyerId }}">
                @if($pendingServiceCharges->count())
                <h5>Pending Service Charges</h5>
                @foreach($pendingServiceCharges as $entry)
                <input type="checkbox" name="service_entry_ids[]" class="payment-checkbox" value="{{ $entry->id }}"
                    data-amount="{{ $entry->total_amount }}">
                Entry #{{ $entry->id }} -
                Amount: {{ $entry->total_amount }}<br>
                @endforeach
                @endif

                @if($pendingLoanRepayments->count())
                <h5>Pending Loan Repayments</h5>
                @foreach($pendingLoanRepayments as $repayment)
                <input type="checkbox" name="repayment_ids[]" class="payment-checkbox" value="{{ $repayment->id }}"
                    data-amount="{{ $repayment->amount }}">
                Repayment #{{ $repayment->id }}
                - Amount: {{ $repayment->amount }}<br>
                @endforeach
                @endif

                @if($pendingOtherIncomes->count())
                <h5>Pending Other Incomes</h5>
                @foreach($pendingOtherIncomes as $pendingOtherIncome)
                <input type="checkbox" name="otherincome_ids[]" class="payment-checkbox"
                    value="{{ $pendingOtherIncome->id }}" data-amount="{{ $pendingOtherIncome->amount }}">
                OtherIncome #{{ $pendingOtherIncome->id }}
                - Amount: {{ $pendingOtherIncome->amount }}<br>
                @endforeach
                @endif

                @if($pendingServiceCharges->count() || $pendingLoanRepayments->count() || $pendingOtherIncomes->count())
                <button type="submit" class="btn btn-success">Settle Selected</button>
                @endif
            </form>
            @endif
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
    function calculateTotal() {
        let total = 0;
        $('.payment-checkbox:checked').each(function() {
            let amount = parseFloat($(this).data('amount'));
            if (!isNaN(amount)) {
                total += amount;
            }
        });
        $('#totalAmount').text(total.toFixed(2));
    }


    // Calculate on page load
    calculateTotal();

    // Recalculate on checkbox change
    $('.payment-checkbox').change(function() {
        calculateTotal();
    });
});
</script>
@endpush