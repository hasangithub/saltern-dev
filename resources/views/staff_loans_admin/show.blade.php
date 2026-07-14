@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Staff Loan')
@section('content_header_subtitle', 'Welcome')
@section('plugins.Datatables', true)

{{-- Content body: main page content --}}

@section('content_body')

<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="card card-primary">

        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user"></i> Staff Loan Details
            </h3>
        </div>


        <div class="card-body">


            <!-- Staff Information -->

            <h6 class="text-primary">
                Staff Information
            </h6>

            <div class="row mb-3">

                <div class="col-md-4 col-6">
                    <small class="text-muted">Name</small>
                    <p class="mb-0 font-weight-bold">
                        {{ $ownerLoan->user->name }}
                    </p>
                </div>


                <div class="col-md-4 col-6">
                    <small class="text-muted">Email</small>
                    <p class="mb-0">
                        {{ $ownerLoan->user->email ?? '-' }}
                    </p>
                </div>


                <div class="col-md-4 col-6">
                    <small class="text-muted">Loan ID</small>
                    <p class="mb-0 font-weight-bold">
                        #{{ $ownerLoan->id }}
                    </p>
                </div>

            </div>


            <hr>


            <!-- Loan Information -->


            <h6 class="text-primary">
                Loan Information
            </h6>


            <div class="row">


                <div class="col-md-3 col-6">
                    <small class="text-muted">
                        Request Date
                    </small>

                    <p class="mb-0">
                        {{ $ownerLoan->formatted_date }}
                    </p>
                </div>



                <div class="col-md-3 col-6">

                    <small class="text-muted">
                        Requested Amount
                    </small>

                    <p class="mb-0">
                        Rs. {{ number_format($ownerLoan->requested_amount,2) }}
                    </p>

                </div>



                <div class="col-md-3 col-6">

                    <small class="text-muted">
                        Approved Amount
                    </small>

                    <p class="mb-0 font-weight-bold">
                        Rs. {{ number_format($ownerLoan->approved_amount,2) }}
                    </p>

                </div>



                <div class="col-md-3 col-6">

                    <small class="text-muted">
                        Status
                    </small>

                    <p class="mb-0">

                        @if($ownerLoan->status == 'approved')

                            <span class="badge badge-success">
                                Approved
                            </span>

                        @elseif($ownerLoan->status == 'pending')

                            <span class="badge badge-warning">
                                Pending
                            </span>

                        @else

                            <span class="badge badge-danger">
                                {{ ucfirst($ownerLoan->status) }}
                            </span>

                        @endif

                    </p>

                </div>


            </div>


            @if ($ownerLoan->status === 'pending')

                <hr>

                <button type="button"
                    class="btn btn-success btn-sm"
                    data-toggle="modal"
                    data-target="#approveLoanModal">

                    <i class="fas fa-check"></i>
                    Approve Loan Request

                </button>

            @endif


        </div>

    </div>


</div>


<!-- Approve Modal -->

<div class="modal fade" id="approveLoanModal">

    <div class="modal-dialog">

        <div class="modal-content">

            <form action="{{ route('admin.staff-loan.approve', $ownerLoan->id) }}"
                  method="POST">

                @csrf
                @method('PUT')


                <div class="modal-header">

                    <h5 class="modal-title">
                        Approve Loan Request
                    </h5>

                    <button type="button"
                            class="close"
                            data-dismiss="modal">
                        &times;
                    </button>

                </div>


                <div class="modal-body">


                    <div class="form-group">

                        <label>
                            Approved Amount
                        </label>

                        <input type="number"
                               class="form-control"
                               name="approved_amount"
                               value="{{ $ownerLoan->approved_amount }}"
                               required>

                    </div>


                    <div class="form-group">

                        <label>
                            Comments
                        </label>

                        <textarea class="form-control"
                                  name="approval_comments"
                                  rows="3"></textarea>

                    </div>


                </div>


                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary btn-sm"
                            data-dismiss="modal">
                        Cancel
                    </button>


                    <button type="submit"
                            class="btn btn-primary btn-sm">
                        Save
                    </button>

                </div>


            </form>

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
    $('#buyersTable').DataTable();
});
</script>
@endpush