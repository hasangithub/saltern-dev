@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Inventory')
@section('content_header_subtitle', 'Inventory')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <a href="{{ route('inventories.create') }}" class="btn btn-primary mb-3">Add Inventory</a>
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    <div class="table-responsive">
        <table id="inventory-table" class="table table-sm nowrap table-hover" style="width:100%">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Voucher ID</th>
                    <th>Name</th>
                    <th>Place</th>
                    <th>Stock Code</th>
                    <th>Qty</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Replaced From</th>
                    <th>Warranty Period</th>
                    <th>Expired</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inventories as $inventory)
                <tr>
                    <td>{{ optional($inventory->date_of_purchase)->format('Y-m-d') }}</td>
                    <td>{{ $inventory->voucher_id }}</td>
                    <td>{{ $inventory->name }}</td>
                    <td>{{ $inventory->place }}</td>
                    <td>{{ $inventory->stock_code }}</td>
                    <td>{{ $inventory->qty }}</td>
                    <td>{{ number_format($inventory->amount, 2) }}</td>
                    <td>{{ $inventory->description }}</td>
                    <td>{{ $inventory->replacedInventory?->name ?? '-' }}</td>
                    <td>
                        @if($inventory->warranty_from && $inventory->warranty_to)
                        {{ $inventory->warranty_from->format('Y-m-d') }} -
                        {{ $inventory->warranty_to->format('Y-m-d') }}
                        @endif
                    </td>
                    <td>
                        @if($inventory->warranty_to && $inventory->warranty_to < now()) Yes @else No @endif </td>
                    <td>{{ ucfirst($inventory->status) }}</td>
                    <td>
                        <a href="{{ route('inventories.edit', $inventory->id) }}"
                            class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('inventories.destroy', $inventory->id) }}" method="POST"
                            style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
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
    $('#inventory-table').DataTable({
        order: [
            [0, 'desc']
        ],
        pageLength: 100
    });
});
</script>
@endpush