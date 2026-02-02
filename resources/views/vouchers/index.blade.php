@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Vouchers')
@section('content_header_subtitle', 'Welcome')
@section('page-buttons')
<a href="{{ route('vouchers.create') }}" class="btn btn-success ml-auto"> <i class="fas fa-plus"></i> Create Voucher</a>
@endsection
{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table id="voucher-table" class="table table-sm nowrap table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>VoucherId</th>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                    <th>Bank</th>
                                    <th>Ledger/SubLedger</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                        </table>
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
    $('#voucher-table').DataTable({
        processing: true,
        serverSide: true,
        ordering: false, // disables column sorting
        searching: true, // disables search filter
        pageLength: 50,
        ajax: '{{ route("vouchers.data") }}',
        columns: [{
                data: 'id'
            },
            {
                data: 'created_at'
            },
            {
                data: 'name'
            },
            {
                data: 'amount'
            },
            {
                data: 'description'
            },
            {
                data: 'bank'
            },
            {
                data: 'ledger'
            },
            {
                data: 'status'
            },
            {
                data: 'action',
                orderable: false,
                searchable: false
            }
        ]
    });
});
</script>
@endpush