@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Create Owner Loan')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-md">
                            <form action="{{ route('admin.owner_loans.store') }}" method="POST" autocomplete="off">
                                @csrf

                                <div class="form-group row">
                                    <label for="side_id" class="col-sm-3 col-form-label">Side</label>
                                    <div class="col-sm-9">
                                        <select id="side_id" name="side_id" class="form-control" required>
                                            <option value="">-- Select Side --</option>
                                            @foreach ($sides as $side)
                                            <option value="{{ $side->id }}">{{ ucfirst($side->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="yahai_id" class="col-sm-3 col-form-label">Yahai</label>
                                    <div class="col-sm-9">
                                        <select id="yahai_id" name="yahai_id" class="form-control" required>
                                            <option value="">-- Select Yahai --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="saltern_id" class="col-sm-3 col-form-label">Waikal No</label>
                                    <div class="col-sm-9">
                                        <select id="saltern_id" name="saltern_id" class="form-control" required>
                                            <option value="">-- Select Waikal No --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="owner_full_name" class="col-sm-3 col-form-label">Owner</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="membership_name" id="membership_name"
                                            class="form-control" required readonly>
                                        <input type="hidden" name="membership_id" id="membership_id"
                                            class="form-control" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="loan_type" class="col-sm-3 col-form-label">Loan Type</label>
                                    <div class="col-sm-9">
                                        <select id="loan_type" name="loan_type" class="form-control" required>
                                            <option value="">-- Select Type --</option>
                                            <option value="old">Old</option>
                                            <option value="new">New</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Income Categories (initially hidden) --}}
                                <div class="" id="income_category_div" style="display:none;">
                                    <div class="form-group row">
                                        <label for="income_category" class="col-sm-3 col-form-label">Income</label>
                                        <div class="col-sm-9">
                                            <select name="income_category_id" id="income_category" class="form-control">
                                                <option value="">-- Select Income Category --</option>
                                                @foreach($incomeCategories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label for="loan_amount" class="col-sm-3 col-form-label">Loan Amount</label>
                                    <div class="col-sm-9">
                                        <input type="number" name="loan_amount" class="form-control" required>
                                    </div>

                                </div>

                                <div class="mb-3">
                                    <label for="reason" class="form-label">Reason (Optional)</label>
                                    <textarea name="purpose" id="purpose"
                                        class="form-control">{{ old('reason') }}</textarea>
                                </div>

                                <div class="col-12">
                                    <div class="card card-default">
                                        <div class="card-header">
                                            <h3 class="card-title">Loan Details</h3>
                                        </div>

                                        <div class="card-body">
                                            <div id="saltern_details" class="mt-4">

                                            </div>
                                        </div>
                                    </div>
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

    const loanType = document.getElementById('loan_type');
    const incomeDiv = document.getElementById('income_category_div');

    loanType.addEventListener('change', function() {
        if (this.value === 'old') {
            incomeDiv.style.display = 'block';
        } else {
            incomeDiv.style.display = 'none';
            document.getElementById('income_category').value = '';
        }
    });

    const $form = $('form');
    const $submitBtn = $form.find('button[type="submit"]');

    // Re-enable submit button on page load in case it was disabled before
    if ($submitBtn.prop('disabled')) {
        $submitBtn.prop('disabled', false);
        $submitBtn.text('Save'); // Reset to your default button text
    }

    $form.on('submit', function() {
        $submitBtn.prop('disabled', true);
        $submitBtn.text('Submitting...');
    });

    $('#side_id').change(function() {
        const sideId = $(this).val();

        // Reset and disable the Yahai dropdown
        $('#yahai_id').prop('disabled', true).empty().append('<option value="">Select Yahai</option>');

        // Reset and disable the Saltern dropdown
        $('#saltern_id').prop('disabled', true).empty().append(
            '<option value="">Select Saltern</option>');

        if (sideId) {
            $.ajax({
                url: "{{ route('get.yahai') }}",
                type: "GET",
                data: {
                    side_id: sideId
                },
                success: function(response) {
                    response.yahais.forEach(yahai => {
                        $('#yahai_id').append(
                            `<option value="${yahai.id}">${yahai.name}</option>`
                        );
                    });
                    $('#yahai_id').prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching Yahais:', error);
                }
            });
        }
    });

    $('#yahai_id').change(function() {
        const yahaiId = $(this).val();
        $('#saltern_id').prop('disabled', true).empty().append(
            '<option value="">Select Saltern</option>');

        if (yahaiId) {
            $.ajax({
                url: "{{ route('get.saltern') }}",
                type: "GET",
                data: {
                    yahai_id: yahaiId
                },
                success: function(response) {
                    response.salterns.forEach(saltern => {
                        $('#saltern_id').append(
                            `<option value="${saltern.id}">${saltern.name}</option>`
                        );
                    });
                    $('#saltern_id').prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching salterns :', error);
                }
            });
        }
    });

    $('#saltern_id').change(function() {
        const salternId = $(this).val(); // Get selected saltern ID

        if (salternId) {
            $.ajax({
                url: "{{ route('get.membership', '') }}/" + salternId,
                type: "GET",
                success: function(response) {
                    if (response.status === 'success') {
                        const membership = response.membership;
                        const owner = response.owner;
                        // Populate the form with membership details
                        $('#membership_id').val(membership.id);
                        $('#membership_name').val(owner.name_with_initial);
                    } else {
                        alert('No membership found for this saltern');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching membership details:', error);
                }
            });

            $.ajax({
                url: "{{ route('get.loan.details', '') }}/" + salternId,
                type: "GET",
                success: function(loans) {
                    $('#saltern_details').html(loans);
                },
                error: function() {
                    $('#saltern_details').html(
                        '<p>An error occurred. Please try again.</p>');
                },
            });
        }
    });
});
</script>
@endpush