@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Other Income')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Other Income</h3>
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
                    <form action="{{ route('other_incomes.store') }}" method="POST" autocomplete="off">
                        @csrf
                        <div class="form-group">
                            <label for="buyer_id">Buyer</label>
                            <select name="buyer_id" id="buyer_id" class="form-control" required>
                                <option value="">Select Buyer</option>
                                <option value="walkin">Walk-in Buyer</option>
                                @foreach ($buyers as $buyer)
                                <option value="{{ $buyer->id }}">{{ $buyer->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" id="walkin_name_field">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="number" name="amount" id="amount" class="form-control" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="ledger">Select Ledger:</label>
                            <select name="ledger_id" id="ledger" class="form-control" required>
                                <option value="">-- Select Ledger --</option>
                                @foreach ($ledgers as $ledger)
                                <option value="{{ $ledger->id }}">{{ $ledger->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="sub_ledger">Select Sub-Ledger:</label>
                            <select name="sub_ledger_id" id="sub_ledger" class="form-control">
                                <option value="">-- Select Sub-Ledger --</option>
                            </select>
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
    document.getElementById('ledger').addEventListener('change', function () {
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

    $(document).ready(function(){
        function toggleWalkinField() {
            if($('#buyer_id').val() === 'walkin') {
                $('#walkin_name_field').show();
            } else {
                $('#walkin_name_field').hide();
            }
        }

        // Initial check
        toggleWalkinField();

        // On change
        $('#buyer_id').change(function(){
            toggleWalkinField();
        });
    });
</script>
@endpush