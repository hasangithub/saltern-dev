@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Create Staff Loan')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Create new Loan for staff</h3>
                </div>

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
                        <div class="col-md-6">
                            <form action="{{ route('admin.staff_loans.store') }}" method="POST" autocomplete="off">
                                @csrf

                                <div class="form-group row">
                                    <label for="user_id" class="col-sm-3 col-form-label">Staff Name</label>
                                    <div class="col-sm-9">
                                        <select id="user_id" name="user_id" class="form-control" required>
                                            <option></option>
                                            @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ ucfirst($user->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="loan_type_oldnew" class="col-sm-3 col-form-label">Old/New Type</label>
                                    <div class="col-sm-9">
                                        <select id="loan_type_oldnew" name="loan_type_oldnew" class="form-control" required>
                                            <option value="">-- Select Type --</option>
                                            <option value="old">Old</option>
                                            <option value="new">New</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="loan_type" class="col-sm-3 col-form-label">Loan Type</label>
                                    <div class="col-sm-9">
                                        <select id="loan_type" name="loan_type" class="form-control" required>
                                            <option value="">-- Select Type --</option>
                                            <option value="loan">Loan</option>
                                            <option value="festival loan">Festival Loan</option>
                                        </select>
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


    $('#user_id').change(function() {
        const userId = $(this).val(); // Get selected saltern ID

        if (userId) {
    
            $.ajax({
                url: "{{ route('get.staff.loan.details', '') }}/" + userId,
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