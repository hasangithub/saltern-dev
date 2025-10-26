@extends('layout.app')

@section('subtitle', 'Welcome')
@section('content_header_title', 'Inventory')
@section('content_header_subtitle', 'Create')

@section('content_body')
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">Create Inventory</h3>
    </div>

    <div class="card-body">
    <form method="POST" action="{{ route('inventories.store') }}">
        @csrf

        <div class="container-fluid">
            <div class="row g-3">

                <div class="col-md-6 col-lg-4">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="col-md-6 col-lg-4">
                    <label for="stock_code" class="form-label">Stock Code</label>
                    <input type="text" name="stock_code" id="stock_code" class="form-control"
                        value="{{ old('stock_code', $inventory->stock_code ?? '') }}">
                </div>

                <div class="col-md-6 col-lg-4">
                    <label for="qty" class="form-label">Quantity</label>
                    <input type="number" name="qty" id="qty" class="form-control"
                        value="{{ old('qty', $inventory->qty ?? '') }}">
                </div>

                <div class="col-md-6 col-lg-4">
                    <label for="date_of_purchase" class="form-label">Date of Purchase</label>
                    <input type="date" name="date_of_purchase" id="date_of_purchase" class="form-control"
                        value="{{ old('date_of_purchase', isset($inventory->date_of_purchase) ? $inventory->date_of_purchase->format('Y-m-d') : '') }}">
                </div>

                <div class="col-md-6 col-lg-4">
                    <label for="warranty_from" class="form-label">Warranty From</label>
                    <input type="date" name="warranty_from" id="warranty_from" class="form-control"
                        value="{{ old('warranty_from', isset($inventory->warranty_from) ? $inventory->warranty_from->format('Y-m-d') : '') }}">
                </div>

                <div class="col-md-6 col-lg-4">
                    <label for="warranty_to" class="form-label">Warranty To</label>
                    <input type="date" name="warranty_to" id="warranty_to" class="form-control"
                        value="{{ old('warranty_to', isset($inventory->warranty_to) ? $inventory->warranty_to->format('Y-m-d') : '') }}">
                </div>

                <div class="col-md-6 col-lg-4">
                    <label>Place</label>
                    <select name="place_id" class="form-control" required>
                        <option value="">-- Select Place --</option>
                        @foreach($places as $place)
                        <option value="{{ $place->id }}">{{ ucfirst($place->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 col-lg-4">
                    <label>Amount</label>
                    <input type="number" step="0.01" name="amount" class="form-control" required>
                </div>

                <div class="col-md-6 col-lg-4">
                    <label>Voucher ID</label>
                    <input type="text" name="voucher_id" class="form-control">
                </div>

                <div class="col-12 col-lg-8">
                    <label>Description</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>

                <div class="col-md-6 col-lg-4">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="inuse">In Use</option>
                        <option value="repaired">Repaired</option>
                        <option value="replaced">Replaced</option>
                    </select>
                </div>

                <div class="col-12 col-lg-8">
                    <label>Replaced Inventory</label>
                    <select name="replaced_id" class="form-control select2">
                        <option value="">-- None --</option>
                        @foreach($inventories as $inv)
                        <option value="{{ $inv->id }}">{{ $inv->name." ". $inv->place->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 text-end mt-3">
                    <button type="submit" class="btn btn-success">Save</button>
                </div>

            </div>
        </div>
    </form>
</div>

    @endsection

    {{-- Push extra scripts --}}

@push('js')
<script>
$(document).ready(function() {
    $('.select2').select2();
});
</script>
@endpush