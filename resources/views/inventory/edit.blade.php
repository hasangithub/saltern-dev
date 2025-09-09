@extends('layout.app')

@section('subtitle', 'Welcome')
@section('content_header_title', 'Inventory')
@section('content_header_subtitle', 'Edit')

@section('content_body')
<div class="container-fluid">
    <form method="POST" action="{{ route('inventories.update', $inventory->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" value="{{ old('name', $inventory->name) }}" class="form-control" required>
        </div>

        {{-- Stock Code --}}
        <div class="col-md-2 mb-3">
            <label for="stock_code" class="form-label">Stock Code</label>
            <input type="text" name="stock_code" id="stock_code" class="form-control"
                value="{{ old('stock_code', $inventory->stock_code ?? '') }}">
        </div>

        {{-- Quantity --}}
        <div class="col-md-2 mb-3">
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
                <option value="yard" {{ old('place', $inventory->place) == 'yard' ? 'selected' : '' }}>Yard</option>
                <option value="office" {{ old('place', $inventory->place) == 'office' ? 'selected' : '' }}>Office</option>
            </select>
        </div>
        
        <div class="mb-3">
            <label>Amount</label>
            <input type="number" step="0.01" name="amount" value="{{ old('amount', $inventory->amount) }}" class="form-control" required>
        </div>
        
        <div class="mb-3">
            <label>Voucher ID</label>
            <input type="text" name="voucher_id" value="{{ old('voucher_id', $inventory->voucher_id) }}" class="form-control">
        </div>
        
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control">{{ old('description', $inventory->description) }}</textarea>
        </div>
        
        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="inuse" {{ old('status', $inventory->status) == 'inuse' ? 'selected' : '' }}>In Use</option>
                <option value="repaired" {{ old('status', $inventory->status) == 'repaired' ? 'selected' : '' }}>Repaired</option>
                <option value="replaced" {{ old('status', $inventory->status) == 'replaced' ? 'selected' : '' }}>Replaced</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Replaced Inventory</label>
            <select name="replaced_id" class="form-control">
                <option value="">-- None --</option>
                @foreach($inventories as $inv)
                    <option value="{{ $inv->id }}" 
                        {{ old('replaced_id', $inventory->replaced_id) == $inv->id ? 'selected' : '' }}>
                        {{ $inv->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('inventories.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
