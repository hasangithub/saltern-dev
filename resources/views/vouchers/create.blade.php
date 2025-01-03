@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Create Vouchers')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">Create Vouchers</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
            </button>
        </div>
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
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif
        <form id="transactionForm" action="{{ route('vouchers.store') }}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <!-- Name -->
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}"
                            required>
                    </div>

                    <!-- Address -->
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" name="address" id="address" class="form-control" value="{{ old('address') }}"
                            required>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control"
                            required>{{ old('description') }}</textarea>
                    </div>

                    <!-- Payment Method -->
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <select name="payment_method_id" id="payment_method" class="form-control" required>
                            <option value=""></option>
                            @foreach($paymentMethods as $method)
                            <option value="{{ $method->id }}">{{ $method->payment_method_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div id="bankDetails" class="row" style="display: none;">
                        <!-- Bank -->
                        <div class="form-group">
                            <label for="bank">Bank</label>
                            <select name="bank_id" id="bank" class="form-control">
                                <option value=""></option>
                                @foreach($banks as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->bank_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Cheque Number -->
                        <div class="form-group">
                            <label for="cheque_no">Cheque Number</label>
                            <input type="text" name="cheque_no" id="cheque_no" class="form-control"
                                value="{{ old('cheque_no') }}">
                        </div>
                        <!-- Cheque Date -->
                        <div class="form-group">
                            <label for="cheque_date">Cheque Date</label>
                            <input type="date" name="cheque_date" id="cheque_date" class="form-control"
                                value="{{ old('cheque_date') }}">
                        </div>
                    </div>
                    <!-- Amount -->
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" step="0.01" name="amount" id="amount" class="form-control"
                            value="{{ old('amount') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="ledger">Select Ledger:</label>
                        <select id="ledger" class="form-control" required>
                            <option value="">-- Select Ledger --</option>
                            @foreach ($ledgers as $ledger)
                            <option value="{{ $ledger->id }}">{{ $ledger->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sub_ledger">Select Sub-Ledger:</label>
                        <select id="sub_ledger" class="form-control" required>
                            <option value="">-- Select Sub-Ledger --</option>
                        </select>
                    </div>


                    <!-- Note -->
                    <div class="form-group">
                        <label for="note">Note</label>
                        <textarea name="note" id="note" class="form-control">{{ old('note') }}</textarea>
                    </div>
                </div>
            </div>

            <button type="submit" id="saveTransaction" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Voucher
            </button>
        </form>
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
<!-- SweetAlert2 CDN -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.7/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.7/dist/sweetalert2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentType = document.getElementById('payment_method');
    const bankDetails = document.getElementById('bankDetails');

    paymentType.addEventListener('change', function() {
        if (paymentType.value === '1') {
            bankDetails.style.display = 'block'; // Show bank details
            document.getElementById('cheque_no').setAttribute('required', 'true');
            document.getElementById('cheque_date').setAttribute('required', 'true');
            document.getElementById('bank_id').setAttribute('required', 'true');
        } else {
            bankDetails.style.display = 'none'; // Hide bank details
            document.getElementById('cheque_no').removeAttribute('required');
            document.getElementById('cheque_date').removeAttribute('required');
            document.getElementById('bank_id').removeAttribute('required');
        }
    });

    document.getElementById('ledger').addEventListener('change', function () {
        const ledgerId = this.value;
        const subLedgerSelect = document.getElementById('sub_ledger');
        
        // Clear the sub-ledger dropdown
        subLedgerSelect.innerHTML = '<option value="">-- Select Sub-Ledger --</option>';

        if (ledgerId) {
            const fetchSubledgersUrl = "{{ route('api.subledgers', ':ledgerId') }}";
            const url = fetchSubledgersUrl.replace(':ledgerId', ledgerId);
            // Make AJAX request to fetch sub-ledgers
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Populate sub-ledger dropdown
                    data.forEach(subLedger => {
                        const option = document.createElement('option');
                        option.value = subLedger.id;
                        option.textContent = subLedger.name;
                        subLedgerSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching sub-ledgers:', error));
        }
    });
});
</script>
@endpush