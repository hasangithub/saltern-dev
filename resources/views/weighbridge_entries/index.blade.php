@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Yahai')
@section('content_header_title', 'Weighbridge')
@section('content_header_subtitle', 'Welcome')
@section('plugins.Datatables', true)
@section('page-buttons')
<a href="{{ route('weighbridge_entries.create') }}" class="btn btn-success float-right"> <i class="fas fa-plus"></i>
    Create
    Entry</a>
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
                        <table id="weighbridge-table" class="table table-sm nowrap table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Date</th>
                                    <th>Turn</th>
                                    <th>Buyer</th>
                                    <th>Vehicle ID</th>
                                    <th>Owner</th>
                                    <th>Yahai</th>
                                    <th>Waikal</th>
                                    <th>Weight</th>
                                    <th>Bags</th>
                                    <th>Amount</th>
                                    <th>Bill</th>
                                    <th>Loan</th>
                                    <th></th> {{-- For Action buttons --}}
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
    $('#weighbridge-table').DataTable({
        processing: true,
        serverSide: true,
        ordering: false, // disables column sorting
        searching: true, // disables search filter
        pageLength: 50,
        ajax: '{{ route("weighbridge_entries.data") }}',
        columns: [{
                data: 'id'
            },
            {
                data: 'transaction_date'
            },
            {
                data: 'turn_no'
            },
            {
                data: 'buyer_name'
            },
            {
                data: 'vehicle_id'
            },
            {
                data: 'owner_name'
            },
            {
                data: 'yahai_name'
            },
            {
                data: 'waikal'
            },
            {
                data: 'net_weight'
            },
            {
                data: 'bags'
            },
            {
                data: 'amount'
            },
            {
                data: 'receipt'
            },
            {
                data: 'loan'
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