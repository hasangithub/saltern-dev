@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Staff Loans')
@section('content_header_subtitle', 'Staff Loans')
@section('page-buttons')
<button class="btn btn-primary me-2" data-toggle="modal" data-target="#addItemModal">
                <i class="fas fa-plus"></i> Add Item
            </button>
@endsection
{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <h4 class="mb-4">Stock Management</h4>
    <!-- Stock Report -->
    <div id="stockReport" class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Item</th>
                        <th>Acton</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $t)
                    <tr>
                        <td>{{ $t->created_at }}</td>
                        <td>{{ $t->name }}</td>
                        <td><a href="{{ route('stock.showItem', $t->id) }}"
                                            class="btn btn-default btn-xs">
                                            <i class="fas fa-eye"></i> View
                                        </a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>

<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('stock.storeItem') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Item</h5>
                    <button type="button" class="btn-close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Item Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Opening Balance (Qty)</label>
                        <input type="number" step="0.01" name="opening_balance" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Item</button>
                </div>
            </div>
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
    $('#membershipsTable').DataTable();
});
</script>
@endpush