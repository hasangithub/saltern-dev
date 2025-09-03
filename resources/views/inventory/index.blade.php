@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Inventory')
@section('content_header_subtitle', 'Inventory')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <a href="{{ route('inventories.create') }}" class="btn btn-primary mb-3">Add Inventory</a>
    
    <table id="inventory-table" class="table table-sm nowrap table-hover" style="width:100%">
        <thead>
            <tr>
                <th>Name</th>
                <th>Place</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Replaced From</th>
                <th>Description</th>
                <th>Voucher</th>
                <th>Created By</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventories as $inventory)
            <tr>
                <td>{{ $inventory->name }}</td>
                <td>{{ ucfirst($inventory->place) }}</td>
                <td>{{ number_format($inventory->amount, 2) }}</td>
                <td>{{ ucfirst($inventory->status) }}</td>
                <td>{{ $inventory->replacedInventory?->name ?? '-' }}</td>
                <td>{{ $inventory->description }}</td>
                <td>{{ $inventory->voucher_id }}</td>
                <td>{{ $inventory->creator->name ?? 'System' }}</td>
                <td>
                    <a href="{{ route('inventories.edit', $inventory) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('inventories.destroy', $inventory) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this inventory?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
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
    $('#inventory-table').DataTable({
        order: [
            [0, 'desc']
        ],
        pageLength: 100
    });
});

</script>
@endpush
