@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Stock Management')
@section('content_header_subtitle', 'Stock')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div id="actionButtons" class="mb-3">
        <button class="btn btn-success me-2" data-toggle="modal" data-target="#addPurchaseModal">
            <i class="fas fa-arrow-down"></i> Add Purchase
        </button>
        <button class="btn btn-danger" data-toggle="modal" data-target="#addIssueModal">
            <i class="fas fa-arrow-up"></i> Add Issue
        </button>
    </div>

    <!-- Stock Report -->
    <div id="stockReport" class="card">
        <div class="card-body">
            <h5 class="card-title">Stock Report - {{ $item->name }}</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Opening Stock</th>
                        <th>Purchase</th>
                        <th>Issued Department</th>
                        <th>Issued Qty</th>
                        <th>Stock in Hand</th>
                        <th>Details</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {{-- First Row: Opening Balance --}}
                    <tr>
                        <td class="text-right">{{ $startDate ?? 'Opening' }}</td>
                        <td class="text-right">{{ $opening }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">{{ $opening }}</td>
                        <td>Opening Balance</td>
                        <td></td>
                    </tr>

                    {{-- Transaction Rows --}}
                    @php $stockInHand = $opening; @endphp
                    @foreach($transactions as $t)
                    @php
                    if ($t->type === 'purchase') {
                    $stockInHand += $t->quantity;
                    } elseif ($t->type === 'issue') {
                    $stockInHand -= $t->quantity;
                    }
                    @endphp
                    <tr>
                        <td>{{ $t->transaction_date }}</td>
                        <td class="text-right"></td> {{-- No opening qty here, only in first row --}}
                        <td class="text-right">{{ $t->type === 'purchase' ? $t->quantity : '' }}</td>
                        <td class="text-right">{{ $t->type === 'issue' ? $t->department : '' }}</td>
                        <td class="text-right">{{ $t->type === 'issue' ? $t->quantity : '' }}</td>
                        <td class="text-right">{{ number_format($stockInHand, 2) }}</td>
                        <td>{{ $t->description }}</td>
                        <td> <button class="btn btn-sm btn-primary edit-btn" data-id="{{ $t->id }}"
                                data-type="{{ $t->type }}" data-quantity="{{ $t->quantity }}"
                                data-date="{{ $t->transaction_date }}" data-department="{{ $t->department }}"
                                data-description="{{ $t->description }}" data-toggle="modal"
                                data-target="#editIssueModal">
                                <i class="fas fa-edit"></i> Edit
                            </button></td>
                    </tr>
                    @endforeach
                </tbody>

            </table>

        </div>
    </div>
</div>

<!-- ================= Add Purchase Modal ================= -->
<div class="modal fade" id="addPurchaseModal" tabindex="-1" aria-labelledby="addPurchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('stock.storeTransaction', $item->id) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Purchase</h5>
                    <button type="button" class="btn-close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="type" value="purchase">
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" step="0.01" name="quantity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}"
                            required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save Purchase</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ================= Add Issue Modal ================= -->
<div class="modal fade" id="addIssueModal" tabindex="-1" aria-labelledby="addIssueModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('stock.storeTransaction', $item->id) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Issue</h5>
                    <button type="button" class="btn-close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="type" value="issue">
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" step="0.01" name="quantity" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}"
                            required>
                    </div>
                    <div class="form-group mb-2">
                        <label for="department">Department</label>
                        <input type="text" class="form-control" name="department" placeholder="e.g. Transport">
                    </div>

                    <div class="form-group mb-2">
                        <label for="description">Description</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Save Issue</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ================= Edit Issue Modal ================= -->
<div class="modal fade" id="editIssueModal" tabindex="-1" aria-labelledby="editIssueModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        {{-- The form action will be set dynamically with JavaScript --}}
        <form id="editIssueForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Issue</h5>
                    <button type="button" class="btn-close" data-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="type" value="issue">

                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" step="0.01" name="quantity" id="edit_quantity" class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="transaction_date" id="edit_transaction_date" class="form-control"
                            required>
                    </div>

                    <div class="form-group mb-2">
                        <label for="department">Department</label>
                        <input type="text" class="form-control" name="department" id="edit_department"
                            placeholder="e.g. Transport">
                    </div>

                    <div class="form-group mb-2">
                        <label for="description">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="2"
                            placeholder="Details..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Issue</button>
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
    $('.edit-btn').on('click', function() {
        const id = $(this).data('id');
        const type = $(this).data('type');
        const quantity = $(this).data('quantity');
        const date = $(this).data('date');
        const department = $(this).data('department');
        const description = $(this).data('description');

        // Populate the modal fields
        $('#edit_type').val(type);
        $('#edit_quantity').val(quantity);
        $('#edit_transaction_date').val(date);
        $('#edit_department').val(department);
        $('#edit_description').val(description);

        // Update form action dynamically
        $('#editIssueForm').attr('action', "{{ url('stock/item') }}/" + id + "/updateTransaction");

    });
});
</script>
@endpush