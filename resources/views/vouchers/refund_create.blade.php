@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Create Refund Voucher')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="card card-default">
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
            <form id="transactionForm" action="{{ route('vouchers.refund.store') }}" method="post" autocomplete="off">
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
                            <input type="text" name="address" id="address" class="form-control"
                                value="{{ old('address') }}" required>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control"
                                required>30% Service Charge Refund</textarea>
                        </div>

                        <div class="form-group">
                            <label for="refund_id">Select Refund</label>
                            <select name="refund_id" id="refund_id" class="form-control" required>
                                <option value="">-- Select Refund --</option>
                                @foreach($refunds as $refund)
                                <option value="{{ $refund->id }}" data-amount="{{ $refund->refund_amount }}"
                                    data-owner="{{ $refund->memberships->owner->name_with_initial }}"
                                    data-yahai="{{ $refund->memberships->saltern->yahai->name }}"
                                    data-saltern="{{ $refund->memberships->saltern->name }}">
                                    {{ $refund->memberships->saltern->yahai->name ?? '-' }} |
                                    {{ $refund->memberships->saltern->name ?? '-' }} |
                                    {{ $refund->memberships->owner->name_with_initial ?? '-' }} -
                                    Rs. {{ number_format($refund->refund_amount,2) }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Payment Method -->
                        <div class="form-group">
                            <label for="payment_method">Payment Method</label>
                            <select name="payment_method_id" id="payment_method" class="form-control" required>
                                <option value=""></option>
                                @foreach($paymentMethods as $method)
                                <option value="{{ $method->id }}" {{ $method->id == 1 ? 'selected' : '' }}>
                                    {{ $method->payment_method_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div id="bankDetails" class="row" style="display: none;">
                            <!-- Bank -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="bank">Bank</label>
                                    <select name="bank_sub_ledger_id" id="bank" class="form-control">
                                        <option value=""></option>
                                        @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}"  {{ $bank->id == 107 ? 'selected' : '' }}>{{ $bank->name }}</option>
                                        @endforeach
                                    </select>
                                    <div id="balanceDisplay" class="mt-2 text-success font-weight-bold"></div>
                                </div>
                            </div>
                            <!-- Cheque Number -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cheque_no">Cheque Number</label>
                                    <input type="text" name="cheque_no" id="cheque_no" class="form-control"
                                        value="{{ old('cheque_no') }}">
                                </div>
                            </div>
                            <!-- Cheque Date -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cheque_date">Cheque Date</label>
                                    <input type="date" name="cheque_date" id="cheque_date" class="form-control"
                                    value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                        <!-- Amount -->
                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="number" step="0.01" name="amount" id="amount" class="form-control"
                                value="{{ old('amount') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="sub_account">Select Sub-Account:</label>
                            <select name="sub_account" id="sub_account" class="form-control subAccount">
                                <option value="">Select SubAccount</option>
                                @foreach($subAccounts as $subAccount)
                                <option value="{{ $subAccount->id }}">{{ $subAccount->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="ledger">Select Ledger:</label>
                            <select name="ledger_id" id="ledger" class="form-control">
                                <option value="">-- Select Ledger --</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="sub_ledger">Select Sub-Ledger:</label>
                            <select name="sub_ledger_id" id="sub_ledger" class="form-control">
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
$(document).ready(function() {

    /* ------------------------------
       AUTO FILL REFUND FIELDS
    ------------------------------ */
    $("#refund_id").on("change", function() {
        let selected = $(this).find(":selected");

        $("#amount").val(selected.data("amount") || "");
        $("#name").val(selected.data("owner") || "");

        let address = "";
        if (selected.data("yahai")) address += selected.data("yahai");
        if (selected.data("saltern")) address += " - " + selected.data("saltern");
        $("#address").val(address.trim());
    });


    /* ------------------------------
       PAYMENT METHOD (DEFAULT BANK)
    ------------------------------ */

    function toggleBankFields() {
        const paymentMethod = $("#payment_method").val();

        if (paymentMethod == "1") { // 1 = BANK
            $("#bankDetails").show();

            $("#cheque_no").attr("required", true);
            $("#cheque_date").attr("required", true);
            $("#bank").attr("required", true);

        } else {
            $("#bankDetails").hide();

            $("#cheque_no").removeAttr("required");
            $("#cheque_date").removeAttr("required");
            $("#bank").removeAttr("required");
        }
    }

    // Run on page load
    toggleBankFields();

    // Run on change
    $("#payment_method").on("change", toggleBankFields);



    /* ------------------------------
       BANK BALANCE FETCH
    ------------------------------ */
    const baseUrlBal = "{{ url('/subledger-balance') }}";

    $("#bank").on("change", function() {
        $("#balanceDisplay").html("");
        const subledgerId = $(this).val();

        if (!subledgerId) return;

        fetch(`${baseUrlBal}/${subledgerId}`)
            .then(res => res.json())
            .then(data => {
                $("#balanceDisplay").html(
                    "Current Balance: Rs. " + Number(data.balance).toLocaleString()
                );
            });
    });

    $("#bank").trigger("change");



    /* ------------------------------
       SUB ACCOUNT → LOAD LEDGERS
    ------------------------------ */
    function loadLedger(subAccountId, defaultLedgerId = null) {
        $('#ledger').prop('disabled', true).empty().append(
            '<option value="">Select Ledgers</option>'
        );

        if (!subAccountId) return;

        $.ajax({
            url: "{{ url('api/subaccount-ledgers') }}/" + subAccountId,
            type: "GET",
            success: function(response) {
                response.forEach(led => {
                    $('#ledger').append(
                        `<option value="${led.id}">${led.name}</option>`
                    );
                });
                $('#ledger').prop('disabled', false);

                // Set default ledger if provided
                if (defaultLedgerId) {
                    $('#ledger').val(defaultLedgerId).trigger('change');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching ledgers :', error);
            }
        });
    }





    /* ------------------------------
       LEDGER → LOAD SUB LEDGERS
    ------------------------------ */
    $("#ledger").on("change", function() {
        const ledgerId = $(this).val();
        const subLedgerSelect = $("#sub_ledger");

        subLedgerSelect.html('<option value="">-- Select Sub-Ledger --</option>');

        if (ledgerId) {
            const url = "{{ route('api.subledgers', ':ledgerId') }}".replace(':ledgerId', ledgerId);

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    data.forEach(sl => {
                        subLedgerSelect.append(
                            `<option value="${sl.id}">${sl.name}</option>`);
                    });
                });
        }
    });

    const defaultSubAccount = '30';
    const defaultLedger = '176';

    $('#sub_account').val(defaultSubAccount);
    loadLedger(defaultSubAccount, defaultLedger);

    $('#sub_account').change(function() {
        const subAccountId = $(this).val();
        loadLedger(subAccountId);
    });

});
</script>

@endpush