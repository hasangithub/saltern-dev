@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Receipts')
@section('content_header_subtitle', 'Receipts')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="card">
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
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
            <h5 class="mb-2 mb-md-0">Pendings - {{$buyerName}}</h5>

            <form method="GET" action="{{ route('receipts.create') }}" class="form-inline">
                <select name="buyer_id" id="buyer_id" class="form-control form-control-sm mr-2"
                    onchange="this.form.submit()">
                    <option value="">-- Select Buyer --</option>
                    @foreach($buyers as $buyer)
                    <option value="{{ $buyer->id }}" {{ request('buyer_id') == $buyer->id ? 'selected' : '' }}>
                        {{ $buyer->full_name }}
                    </option>
                    @endforeach
                </select>
            </form>

            <strong class="text-muted small mt-2 mt-md-0">Total Selected: Rs. <span
                    id="totalAmount">0.00</span></strong>
        </div>


        <div class="card-body" style="overflow-y: auto; max-height: 500px;">
            @if(request('buyer_id'))
            <form method="POST" action="{{ route('receipts.store') }}" id="myForm">
                @csrf
                <input type="hidden" name="buyer_id" value="{{ $buyerId }}">
                <div class="form-group row">
                    <label for="payment_method" class="col-sm-3 col-form-label">Payment Method</label>
                    <div class="col-sm-9">
                        <select id="payment_method" name="payment_method_id" class="form-control" required>
                            <option value="">-- Select Payment Method --</option>
                            @foreach($paymentMethods as $method)
                            <option value="{{ $method->id }}">{{ $method->payment_method_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="bankDetails" class="row" style="display: none;">
                    <!-- Bank -->
                    <div class="form-group row">
                        <label for="bank" class="col-sm-3 col-form-label">Bank</label>
                        <div class="col-sm-9">
                            <select name="bank_sub_ledger_id" id="bank" class="form-control">
                                <option value=""></option>
                                @foreach($banks as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!-- Cheque Number -->
                    <div class="form-group row">
                        <label for="cheque_no" class="col-sm-3 col-form-label">Cheque Number</label>
                        <div class="col-sm-9">
                            <input type="text" name="cheque_no" id="cheque_no" class="form-control"
                                value="{{ old('cheque_no') }}">
                        </div>
                    </div>
                    <!-- Cheque Date -->
                    <div class="form-group row">
                        <label for="cheque_date" class="col-sm-3 col-form-label">Cheque Date</label>
                        <div class="col-sm-9">
                            <input type="date" name="cheque_date" id="cheque_date" class="form-control"
                                value="{{ old('cheque_date') }}">
                        </div>
                    </div>
                </div>
                @if($pendingServiceCharges->count())
                <h5>Pending Service Charges</h5>
                <table class="table table-sm nowrap table-hover">

                    @foreach($pendingServiceCharges as $entry)
                    <tr>
                        <td>
                            <input type="checkbox" name="service_entry_ids[]" class="payment-checkbox"
                                value="{{ $entry->id }}" data-amount="{{ $entry->total_amount }}">
                            <strong>Entry #{{ $entry->id }}</strong>
                        </td>
                        <td class="text-right">
                            Rs. {{ number_format($entry->total_amount, 2) }}
                        </td>
                        <td class="text-right">
                            {{ \Carbon\Carbon::parse($entry->transaction_date)->format('Y-m-d') }}
                        </td>
                        <td>
                            {{ $entry->membership->owner->name_with_initial }}
                        </td>
                        <td>
                            {{ $entry->membership->saltern->yahai->name }} - {{ $entry->membership->saltern->name }}
                        </td>
                        <td class="text-right">
                            {{ $entry->net_weight }} kg
                        </td>
                    </tr>
                    @endforeach

                </table>
                @endif

                @if($pendingLoanRepayments->count())
                <h5>Pending Loan Repayments</h5>
                <table class="table table-sm nowrap table-hover">
                    @foreach($pendingLoanRepayments as $repayment)
                    <tr>
                        <td>
                            <input type="checkbox" name="repayment_ids[]" class="payment-checkbox"
                                value="{{ $repayment->id }}" data-amount="{{ $repayment->amount }}">
                            <strong>Loan# {{$repayment->owner_loan_id}}
                                Repayment #{{ $repayment->id }} </strong>
                        </td>
                        <td class="text-right">
                            Rs. {{ $repayment->amount }}
                        </td>
                        <td class="text-right">
                            {{$repayment->repayment_date}}
                        </td>
                        <td>
                            {{ $repayment->ownerLoan->membership->owner->name_with_initial }}
                        </td>
                        <td>
                            {{ $repayment->ownerLoan->membership->saltern->yahai->name }} -
                            {{ $repayment->ownerLoan->membership->saltern->name }}
                        </td>
                    </tr>
                    @endforeach
                </table>
                @endif

                @if($pendingOtherIncomes->count())
                <h5>Pending Other Incomes</h5>
                <table class="table table-sm nowrap table-hover">
                    @foreach($pendingOtherIncomes as $pendingOtherIncome)
                    <tr>
                        <td>
                            <input type="checkbox" name="otherincome_ids[]" class="payment-checkbox"
                                value="{{ $pendingOtherIncome->id }}" data-amount="{{ $pendingOtherIncome->amount }}">
                            <strong>OtherIncome #{{ $pendingOtherIncome->id }}</strong>
                        </td>
                        <td class="text-right">
                            Rs. {{ $pendingOtherIncome->amount }}
                        </td>
                        <td class="text-right">
                            {{$pendingOtherIncome->received_date}}
                        </td>
                        <td>
                            {{$pendingOtherIncome->incomeCategory->name}}
                        </td>
                    </tr>
                    @endforeach
                </table>
                @endif

                @if($pendingServiceCharges->count() || $pendingLoanRepayments->count() || $pendingOtherIncomes->count())
                <button type="submit" class="btn btn-success" id="submitBtn">Settle Selected</button>
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

    document.getElementById('myForm').addEventListener('submit', function() {
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').innerText = 'Processing...';
    });
    const paymentType = document.getElementById('payment_method');
    const bankDetails = document.getElementById('bankDetails');

    // Reset on page load
    window.addEventListener('DOMContentLoaded', function() {
        paymentType.selectedIndex = 0; // Reset payment method
        bankDetails.style.display = 'none'; // Hide bank details

        // Clear and remove required from bank fields
        document.getElementById('cheque_no').value = '';
        document.getElementById('cheque_date').value = '';
        document.getElementById('bank').selectedIndex = 0;

        document.getElementById('cheque_no').removeAttribute('required');
        document.getElementById('cheque_date').removeAttribute('required');
        document.getElementById('bank').removeAttribute('required');
    });

    // Listen for changes
    paymentType.addEventListener('change', function() {
        if (paymentType.value === '1') {
            bankDetails.style.display = 'block';
            document.getElementById('cheque_no').setAttribute('required', 'true');
            document.getElementById('cheque_date').setAttribute('required', 'true');
            document.getElementById('bank').setAttribute('required', 'true');
        } else {
            bankDetails.style.display = 'none';
            document.getElementById('cheque_no').removeAttribute('required');
            document.getElementById('cheque_date').removeAttribute('required');
            document.getElementById('bank').removeAttribute('required');

            // Optional: clear values
            document.getElementById('cheque_no').value = '';
            document.getElementById('cheque_date').value = '';
            document.getElementById('bank').selectedIndex = 0;
        }
    });


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