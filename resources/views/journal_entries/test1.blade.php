@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Create Journal Entry')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">Create Entry</h3>

    </div>

    <div class="card-body table-responsive">
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
        <form id="transactionForm">
            @csrf
            <table class="table table-bordered table-sm" id="transactionTable">
                <thead>
                    <tr>
                        <th>Ledger</th>
                        <th>Subledger</th>
                        <th>Description</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="formRows">
                    <tr>
                        <td>
                            <select name="details[0][ledger]" class="form-control ledger" required>
                                <option value="">Select Ledger</option>
                                @foreach($ledgers as $ledger)
                                <option value="{{ $ledger->id }}">{{ $ledger->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select name="details[0][subledger]" class="form-control subledger">
                                <option value="">Select Subledger</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="details[0][description]"
                                placeholder="Description" required>
                        </td>
                        <td>
                            <input type="number" class="form-control debit" name="details[0][debit]" placeholder="Debit"
                                min="0" required>
                        </td>
                        <td>
                            <input type="number" class="form-control credit" name="details[0][credit]"
                                placeholder="Credit" min="0" required>
                        </td>
                        <td>

                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Total</strong></td>
                        <td id="totalDebit" class="text-right">0.00</td>
                        <td id="totalCredit" class="text-right">0.00</td>
                    </tr>
                </tfoot>
            </table>
            <button type="button" id="addRow" class="btn btn-primary">Add Row</button>
            <button type="button" id="saveTransaction" class="btn btn-success">Save</button>
        </form>
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
<!-- SweetAlert2 CDN -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.7/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.7/dist/sweetalert2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = 1;

    // Add new row
    document.getElementById('addRow').addEventListener('click', function() {
        let row = `<tr>
           <td>
                <select name="details[${rowIndex}][ledger]" class="form-control ledger" required>
                    <option value="">Select Ledger</option>
                    @foreach($ledgers as $ledger)
                    <option value="{{ $ledger->id }}">{{ $ledger->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="details[${rowIndex}][subledger]" class="form-control subledger">
                    <option value="">Select Subledger</option>
                </select>
            </td>
            <td>
                <input type="text" class="form-control" name="details[${rowIndex}][description]" placeholder="Description" required>
            </td>
            <td>
                <input type="number" class="form-control debit" name="details[${rowIndex}][debit]" placeholder="Debit">
            </td>
             <td>
                <input type="number" class="form-control credit" name="details[${rowIndex}][credit]" placeholder="Credit">
            </td>
            <td>
                <button type="button" class="btn btn-danger removeRow">X</button>
            </td>
        </tr>`;
        document.getElementById('formRows').insertAdjacentHTML('beforeend', row);
        rowIndex++;
    });

    // Handle ledger change and populate subledger dropdown
    document.addEventListener('change', function(event) {
        if (event.target.classList.contains('ledger')) {
            let ledgerId = event.target.value;
            let subledgerDropdown = event.target.closest('tr').querySelector('.subledger');

            // Clear previous subledger options
            subledgerDropdown.innerHTML = '<option value="">Select Subledger</option>';

            if (ledgerId) {
                const fetchSubledgersUrl = "{{ route('api.subledgers', ':ledgerId') }}";
                const url = fetchSubledgersUrl.replace(':ledgerId', ledgerId);
                // Fetch subledgers via an API
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(subledger => {
                            let option = document.createElement('option');
                            option.value = subledger.id;
                            option.textContent = subledger.name;
                            subledgerDropdown.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error fetching subledgers:', error));
            }
        }
    });

    // Delete row
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('removeRow')) {
            event.target.closest('tr').remove();
            updateTransactionDetails();
        }
    });

    // Update totals dynamically
    document.addEventListener('input', function(event) {
        if (event.target.classList.contains('debit') || event.target.classList.contains('credit')) {
            updateTransactionDetails();
        }
    });

    // Function to update transaction details
    function updateTransactionDetails() {
        let totalDebit = 0;
        let totalCredit = 0;

        document.querySelectorAll('.debit').forEach(function(debitInput) {
            totalDebit += parseFloat(debitInput.value) || 0;
        });

        document.querySelectorAll('.credit').forEach(function(creditInput) {
            totalCredit += parseFloat(creditInput.value) || 0;
        });

        document.getElementById('totalDebit').textContent = totalDebit.toFixed(2);
        document.getElementById('totalCredit').textContent = totalCredit.toFixed(2);
    }

    document.getElementById('saveTransaction').addEventListener('click', function(event) {
        event.preventDefault();
        let isValid = true;
        const form = document.getElementById('transactionForm');
        const rows = document.querySelectorAll('#formRows tr');

        // Loop through each row to ensure all fields are filled and either Debit or Credit is filled
        rows.forEach(function(row) {
            const ledger = row.querySelector('[name*="ledger"]');
            const subledger = row.querySelector('[name*="subledger"]');
            const description = row.querySelector('[name*="description"]');
            const debit = row.querySelector('.debit');
            const credit = row.querySelector('.credit');

            // Validate each field
            let rowValid = true;

            // Check Ledger
            if (!ledger.value) {
                ledger.classList.add('is-invalid');
                rowValid = false;
            } else {
                ledger.classList.remove('is-invalid');
                ledger.classList.add('is-valid');
            }

            // Check Subledger
            // if (!subledger.value) {
            //     subledger.classList.add('is-invalid');
            //     rowValid = false;
            // } else {
            //     subledger.classList.remove('is-invalid');
            //     subledger.classList.add('is-valid');
            // }

            // Check Description
            if (!description.value) {
                description.classList.add('is-invalid');
                rowValid = false;
            } else {
                description.classList.remove('is-invalid');
                description.classList.add('is-valid');
            }

            // Check Debit or Credit
            if (!(debit.value || credit.value)) {
                debit.classList.add('is-invalid');
                credit.classList.add('is-invalid');
                rowValid = false;
            } else {
                debit.classList.remove('is-invalid');
                credit.classList.remove('is-invalid');
                debit.classList.add('is-valid');
                credit.classList.add('is-valid');
            }

            if (!rowValid) {
                isValid = false;
                row.classList.add('is-invalid');
            } else {
                row.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fill in all fields and either Debit or Credit in each row.',
            });
            return;
        }

        const fetchJEUrl = "{{ route('journal-entries.store') }}";

        // Show SweetAlert confirmation before saving
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to save the transaction details?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, save it!',
            cancelButtonText: 'No, cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData(document.getElementById('transactionForm'));

                // Make AJAX request to save data
                fetch(fetchJEUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest', // Marks the request as an Ajax request
                            'Accept': 'application/json', // Ensures Laravel responds with JSON
                        }
                    })
                    .then(response => {
                        if (response.status === 422) {
                            // Handle validation errors
                            return response.json().then(data => {
                                let errorMessages = '';
                                for (const field in data.errors) {
                                    errorMessages += `${data.errors[field][0]}<br>`;
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validation Error',
                                    html: errorMessages, // Display error messages
                                    confirmButtonText: 'OK',
                                });
                                throw new Error(
                                'Validation error'); // Prevent further processing
                            });
                        } else if (!response.ok) {
                            // Handle non-validation errors
                            throw new Error('An unexpected error occurred.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'Saved!',
                                'Your transaction has been saved successfully.',
                                'success'
                            );
                            document.getElementById('transactionForm').reset();
                            // Optionally update totals or refresh parts of the UI
                            // updateTotals();
                        } else {
                            Swal.fire(
                                'Error!',
                                data.message ||
                                'There was an error saving the transaction.',
                                'error'
                            );
                        }
                    })
                    .catch(error => {
                        if (error.message !== 'Validation error') {
                            Swal.fire(
                                'Error!',
                                error.message || 'An unexpected error occurred.',
                                'error'
                            );
                        }
                        console.error('Error:', error);
                    });
            }
        });

    });
});
</script>
@endpush