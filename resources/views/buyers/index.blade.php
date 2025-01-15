@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Buyers')
@section('content_header_subtitle', 'Welcome')


{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Buyer List</h3>
                    <a href="{{ route('buyers.create') }}" class="btn btn-success ml-auto"> <i class="fas fa-plus"></i> Create Buyer</a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table id="buyersTable" class="table table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Business Name</th>
                                    <th>Contact Name</th>
                                    <th>Credit Limit</th>
                                    <th>Service Out</th>
                                    <th>Phone Number</th>
                                    <th>Phone Number 2</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($buyers as $buyer)
                                <tr>
                                    <td>{{ $buyer->business_name }}</td>
                                    <td>{{ $buyer->full_name }}</td>
                                    <td>{{ $buyer->credit_limit }}</td>
                                    <td>{{ $buyer->service_out ? 'Yes' : 'No' }}</td>
                                    <td>{{ $buyer->phone_number }}</td>
                                    <td>{{ $buyer->secondary_phone_number }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    Footer
                </div>
                <!-- /.card-footer-->
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
    $('#buyersTable').DataTable();
});
</script>
@endpush