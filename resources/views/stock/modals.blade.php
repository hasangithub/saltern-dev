<!-- ================= Add Item Modal ================= -->
<div class="modal fade" id="addItemModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('stock.storeItem') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add New Item</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Item</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- ================= Add Purchase Modal ================= -->
<div class="modal fade" id="addPurchaseModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" id="purchaseForm">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Purchase</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
<div class="modal fade" id="addIssueModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" id="issueForm">
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
