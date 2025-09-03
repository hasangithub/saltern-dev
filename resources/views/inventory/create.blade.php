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
