@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Create Journal Entry')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">Create Entry</h3>
        <a href="{{ route('sub-account-groups.create') }}" class="btn btn-success btn-sm"> <i class="fas fa-plus"></i>
            New</a>
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
        <form action="{{ route('journal-entries.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="ledger_id">Sub Ledger</label>
                <select name="details[0][sub_ledger_id]" id="ledger_id_0" class="form-control" required>
                    <option value="">Select Ledger</option>
                    @foreach($subLedgers as $subLedger)
                    <option value="{{ $subLedger->id }}">{{ $subLedger->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="amount_type[]">Amount Type</label>
                        <select name="details[0][amount_type]" class="form-control" required>
                            <option value="debit">Debit
                            </option>
                            <option value="credit">
                                Credit</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Amount</label>
                        <input type="text" class="form-control" id="amount_0" name="details[0][amount]" placeholder="">
                    </div>
                </div>

            </div>
            <hr>
            <div class="form-group">
                <label for="ledger_id">Sub Ledger</label>
                <select name="details[1][sub_ledger_id]" id="ledger_id_1" class="form-control" required>
                    <option value="">Select Ledger</option>
                    @foreach($subLedgers as $subLedger)
                    <option value="{{ $subLedger->id }}">{{ $subLedger->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="amount_type[]">Amount Type</label>
                        <select name="details[1][amount_type]" class="form-control" required>
                            <option value="debit">Debit
                            </option>
                            <option value="credit">
                                Credit</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Amount</label>
                        <input type="text" class="form-control" id="amount_1" name="details[1][amount]" placeholder="">
                    </div>
                </div>

            </div>
            <div class="form-group">
                <label for="name">Description</label>
                <textarea class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save
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

@endpush