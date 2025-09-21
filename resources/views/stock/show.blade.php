@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Staff Loans')
@section('content_header_subtitle', 'Staff Loans')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <h4 class="mb-4">Stock Management</h4>
    <!-- Action buttons (hidden until item is selected) -->
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
            <h5 class="card-title">Stock Report</h5>
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
                    </tr>
                </thead>
                <tbody>
                    @php $openingStock = $opening; @endphp
                    @foreach($transactions as $t)
                    <tr>
                        <td>{{ $t->transaction_date }}</td>
                        <td>{{ $openingStock }}</td>
                        <td>{{ $t->type === 'purchase' ? $t->quantity : '' }}</td>
                        <td>{{ $t->type === 'issue' ? $t->department : '' }}</td>
                        <td>{{ $t->type === 'issue' ? $t->quantity : '' }}</td>
                        <td>{{ $t->running_balance }}</td>
                        <td>{{ $t->details }}</td>
                    </tr>
                    @php $openingStock = $t->running_balance; @endphp
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
            <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
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
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="type" value="issue">
          <div class="mb-3">
            <label class="form-label">Quantity</label>
            <input type="number" step="0.01" name="quantity" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-danger">Save Issue</button>
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