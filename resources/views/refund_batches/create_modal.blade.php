<div class="modal fade" id="createBatchModal">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('refund-batches.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5>Create Refund Batch</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Batch Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label>Date From</label>
                            <input type="date" name="date_from" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Date To</label>
                            <input type="date" name="date_to" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group mt-2">
                        <label>Refund %</label>
                        <input type="number" name="refund_percentage" class="form-control" value="30" step="0.01">
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Save Draft</button>
                </div>
            </div>
        </form>
    </div>
</div>
