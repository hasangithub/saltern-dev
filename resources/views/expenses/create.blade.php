@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Expense')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Expense</h3>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <form action="{{ route('expenses.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="received_date">Date</label>
                            <input type="date" name="date" id="date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="expense_category_id">Expense Category</label>
                            <select name="expense_category_id" id="expense_category_id" class="form-control">
                                <option value=""></option>
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="number" name="amount" id="amount" class="form-control" step="0.01" required>
                        </div>
                       
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save
                        </button>
                    </form>
                </div>
            </div>
        </div>
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
document.getElementById('ledger').addEventListener('change', function() {
    const ledgerId = this.value;
    const subLedgerSelect = document.getElementById('sub_ledger');

    // Clear the sub-ledger dropdown
    subLedgerSelect.innerHTML = '<option value="">-- Select Sub-Ledger --</option>';

    if (ledgerId) {
        const fetchSubledgersUrl = "{{ route('api.subledgers', ':ledgerId') }}";
        const url = fetchSubledgersUrl.replace(':ledgerId', ledgerId);
        // Make AJAX request to fetch sub-ledgers
        fetch(url)
            .then(response => response.json())
            .then(data => {
                // Populate sub-ledger dropdown
                data.forEach(subLedger => {
                    const option = document.createElement('option');
                    option.value = subLedger.id;
                    option.textContent = subLedger.name;
                    subLedgerSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching sub-ledgers:', error));
    }
});
</script>
@endpush