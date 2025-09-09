@extends('layout.app')

@section('subtitle', 'Welcome')
@section('content_header_title', 'Inventory')
@section('content_header_subtitle', 'Create')

@section('content_body')
<div class="container-fluid">
    <form method="POST" action="{{ route('inventories.store') }}">
        @csrf
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        {{-- Stock Code --}}
        <div class="col-md-2 mb-3">
            <label for="stock_code" class="form-label">Stock Code</label>
            <input type="text" name="stock_code" id="stock_code" class="form-control"
                value="{{ old('stock_code', $inventory->stock_code ?? '') }}">
        </div>

        {{-- Quantity --}}
        <div class="col-md-12 mb-3">
            <label for="qty" class="form-label">Quantity</label>
            <input type="number" name="qty" id="qty" class="form-control"
                value="{{ old('qty', $inventory->qty ?? '') }}">
        </div>

        {{-- Date of Purchase --}}
        <div class="col-md-2 mb-3">
            <label for="date_of_purchase" class="form-label">Date of Purchase</label>
            <input type="date" name="date_of_purchase" id="date_of_purchase" class="form-control"
                value="{{ old('date_of_purchase', isset($inventory->date_of_purchase) ? $inventory->date_of_purchase->format('Y-m-d') : '') }}">
        </div>

        {{-- Warranty From --}}
        <div class="col-md-2 mb-3">
            <label for="warranty_from" class="form-label">Warranty From</label>
            <input type="date" name="warranty_from" id="warranty_from" class="form-control"
                value="{{ old('warranty_from', isset($inventory->warranty_from) ? $inventory->warranty_from->format('Y-m-d') : '') }}">
        </div>

        {{-- Warranty To --}}
        <div class="col-md-2 mb-3">
            <label for="warranty_to" class="form-label">Warranty To</label>
            <input type="date" name="warranty_to" id="warranty_to" class="form-control"
                value="{{ old('warranty_to', isset($inventory->warranty_to) ? $inventory->warranty_to->format('Y-m-d') : '') }}">
        </div>

        <div class="mb-3">
            <label>Place</label>
            <select name="place" class="form-control" required>
                <option value="yard">Yard</option>
                <option value="office">Office</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Voucher ID</label>
            <input type="text" name="voucher_id" class="form-control">
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="inuse">In Use</option>
                <option value="repaired">Repaired</option>
                <option value="replaced">Replaced</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Replaced Inventory</label>
            <select name="replaced_id" class="form-control">
                <option value="">-- None --</option>
                @foreach($inventories as $inv)
                <option value="{{ $inv->id }}">{{ $inv->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Save</button>
    </form>
</div>
@endsection