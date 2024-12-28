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
        <form id="dynamic-form">
            <div id="form-container">
                <!-- Initial Form -->
                <div class="form-entry">
                    <div class="form-group">
                        <label for="ledger_id_1">Sub Ledger</label>
                        <select name="details[1][sub_ledger_id]" id="ledger_id_1" class="form-control" required>
                            <option value="">Select Ledger</option>
                            <!-- Add dynamic options here -->
                            <option value="1">Ledger 1</option>
                            <option value="2">Ledger 2</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amount_type_1">Amount Type</label>
                                <select name="details[1][amount_type]" id="amount_type_1" class="form-control" required>
                                    <option value="debit">Debit</option>
                                    <option value="credit">Credit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amount_1">Amount</label>
                                <input type="text" class="form-control" id="amount_1" name="details[1][amount]"
                                    placeholder="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-primary" id="newBtn">New</button>
            <button type="button" class="btn btn-secondary" id="prevBtn" disabled>Previous</button>
            <button type="button" class="btn btn-secondary" id="nextBtn" disabled>Next</button>
            <button type="submit" class="btn btn-success">Save</button>
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

<script>
  $(document).ready(function () {
    let currentIndex = 1; // Start from 1 for the first entry
    const formContainer = $('#form-container');
    let formData = []; // To store form data for each form entry

    // Function to create form fields with a given index
    function createForm(index, data = null) {
      return `
        <div class="form-entry">
          <div class="form-group">
            <label for="ledger_id_${index}">Sub Ledger</label>
            <select name="details[${index}][sub_ledger_id]" id="ledger_id_${index}" class="form-control" required>
              <option value="">Select Ledger</option>
              <option value="1" ${data?.sub_ledger_id === "1" ? "selected" : ""}>Ledger 1</option>
              <option value="2" ${data?.sub_ledger_id === "2" ? "selected" : ""}>Ledger 2</option>
            </select>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="amount_type_${index}">Amount Type</label>
                <select name="details[${index}][amount_type]" id="amount_type_${index}" class="form-control" required>
                  <option value="debit" ${data?.amount_type === "debit" ? "selected" : ""}>Debit</option>
                  <option value="credit" ${data?.amount_type === "credit" ? "selected" : ""}>Credit</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="amount_${index}">Amount</label>
                <input type="text" class="form-control" id="amount_${index}" name="details[${index}][amount]" value="${data?.amount || ""}" placeholder="">
              </div>
            </div>
          </div>
        </div>
      `;
    }

    // Function to show form by index
    function showForm(index) {
      formContainer.html(formData[index - 1]); // Index starts from 1
      updateNavigationButtons(); // Update the navigation buttons
    }

    // Update navigation buttons state
    function updateNavigationButtons() {
      $('#prevBtn').prop('disabled', currentIndex <= 1); // Disable previous if at the first form
      $('#nextBtn').prop('disabled', currentIndex >= formData.length); // Disable next if at the last form
    }

    // Add the first form when page loads
    formData.push(createForm(currentIndex));
    showForm(currentIndex);

    // Function to validate the current form before adding a new one
    function validateCurrentForm() {
      const subLedger = $(`[name="details[${currentIndex}][sub_ledger_id]"]`).val();
      const amountType = $(`[name="details[${currentIndex}][amount_type]"]`).val();
      const amount = $(`[name="details[${currentIndex}][amount]"]`).val();

      if (!subLedger || !amountType || !amount) {
        alert("Please fill out all required fields.");
        return false; // Prevent adding a new form if validation fails
      }
      return true;
    }

    // "New" button click handler with validation
    $('#newBtn').on('click', function () {
      // Validate current form before proceeding
      if (!validateCurrentForm()) {
        return; // Stop if validation fails
      }

      // Save current form values into formData
      const currentForm = {
        sub_ledger_id: $(`[name="details[${currentIndex}][sub_ledger_id]"]`).val(),
        amount_type: $(`[name="details[${currentIndex}][amount_type]"]`).val(),
        amount: $(`[name="details[${currentIndex}][amount]"]`).val()
      };
      
      // Store the form data into the formData array for the current index
      formData[currentIndex - 1] = createForm(currentIndex, currentForm); // Replace the current form with updated data
      
      // Add a new empty form
      formData.push(createForm(currentIndex + 1));

      currentIndex++;
      showForm(currentIndex);
    });

    // "Previous" button click handler
    $('#prevBtn').on('click', function () {
      if (currentIndex > 1) {
        currentIndex--;
        showForm(currentIndex);
      }
    });

    // "Next" button click handler
    $('#nextBtn').on('click', function () {
      if (currentIndex < formData.length) {
        currentIndex++;
        showForm(currentIndex);
      }
    });

    // Form submission (Save)
    $('#dynamic-form').on('submit', function (e) {
      e.preventDefault();
      console.log("Saving form data:", formData);
      // Send the data to the server using AJAX here
    });
  });
</script>


@endpush