@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Create Sub Ledger')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">Create Sub Ledger</h3>
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
        <form action="{{ route('sub_ledgers.store') }}" method="POST" autocomplete="off">
            @csrf
            <div class="form-group">
                <label for="account_id">Accounts</label>
                <select id="account_id" name="account_id" class="form-control">
                    <option value="">Select Account</option>
                    @foreach($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="sub_account_id">Sub-Accounts</label>
                <select id="sub_account_id" name="sub_account_id" class="form-control select2" disabled>
                    <option value="">Select Sub-Account</option>
                </select>
                <input type="hidden" id="new_sub_account_name" name="new_sub_account_name" value="">
            </div>

            <div class="form-group">
                <label for="ledger_id">Ledgers</label>
                <select id="ledger_id" name="ledger_id" class="form-control select2" disabled>
                    <option value="">Select Ledger</option>
                </select>
                <input type="hidden" id="new_ledger_name" name="new_ledger_name" value="">
            </div>

            <div class="form-group">
                <label for="sub_ledger_name">Sub-Ledger Name</label>
                <input type="text" id="sub_ledger_name" name="sub_ledger_name" class="form-control"
                    placeholder="Enter Sub-Ledger Name">
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
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

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2();

    // Load Sub-Accounts when an Account is selected
    $('#account_id').change(function() {
        const accountId = $(this).val();
        $('#sub_account_id').prop('disabled', true).empty().append(
            '<option value="">Select Sub-Account</option>');
        if (accountId) {
            $.ajax({
                url: "{{ route('get.sub_accounts') }}",
                type: "GET",
                data: {
                    account_id: accountId
                },
                success: function(response) {
                    response.sub_accounts.forEach(sub_account => {
                        $('#sub_account_id').append(
                            `<option value="${sub_account.id}">${sub_account.name}</option>`
                        );
                    });
                    $('#sub_account_id').prop('disabled', false);
                }
            });
        }
    });

    // Enable "Add New Sub-Account" functionality
    $('#sub_account_id').select2({
        placeholder: "Search or Add Sub-Account",
        tags: true,
        createTag: function(params) {
            return {
                id: params.term,
                text: params.term,
                isNew: true
            };
        },
        templateResult: function(data) {
            if (data.isNew) {
                return `<span class="">Add new: ${data.text}</span>`;
            }
            return data.text;
        },
        escapeMarkup: function(markup) {
            return markup;
        }
    }).on('select2:select', function(e) {
        const data = e.params.data;
        $('#new_sub_account_name').val(data.isNew ? data.text : '');
    });

    // Load Ledgers when a Sub-Account is selected
    $('#sub_account_id').change(function() {
        const subAccountId = $(this).val();
        $('#ledger_id').prop('disabled', true).empty().append(
        '<option value="">Select Ledger</option>');
        if (subAccountId) {
            $.ajax({
                url: "{{ route('get.ledgers') }}",
                type: "GET",
                data: {
                    sub_account_id: subAccountId
                },
                success: function(response) {
                    response.ledgers.forEach(ledger => {
                        $('#ledger_id').append(
                            `<option value="${ledger.id}">${ledger.name}</option>`
                        );
                    });
                    $('#ledger_id').prop('disabled', false);
                }
            });
        }
    });

    // Enable "Add New Ledger" functionality
    $('#ledger_id').select2({
        placeholder: "Search or Add Ledger",
        tags: true,
        createTag: function(params) {
            return {
                id: params.term,
                text: params.term,
                isNew: true
            };
        },
        templateResult: function(data) {
            if (data.isNew) {
                return `<span class="">Add new: ${data.text}</span>`;
            }
            return data.text;
        },
        escapeMarkup: function(markup) {
            return markup;
        }
    }).on('select2:select', function(e) {
        const data = e.params.data;
        $('#new_ledger_name').val(data.isNew ? data.text : '');
    });
});
</script>
@endpush