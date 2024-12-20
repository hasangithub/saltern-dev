@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Buyers')
@section('content_header_subtitle', 'Welcome')
@section('plugins.Datatables', true)

{{-- Content body: main page content --}}

@section('content_body')
<div class="row">
    <div class="card card-default">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Buyer List</h3>
            <a href="{{ route('buyers.create') }}" class="btn btn-primary ml-auto">Create Buyer</a>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <table id="buyersTable" class="table table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Credit Limit</th>
                                <th>Service Out</th>
                                <th>Address 1</th>
                                <th>Address 2</th>
                                <th>Phone No</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($buyers as $buyer)
                            <tr>
                                <td>{{ $buyer->code }}</td>
                                <td>{{ $buyer->name }}</td>
                                <td>{{ $buyer->credit_limit }}</td>
                                <td>{{ $buyer->service_out ? 'Yes' : 'No' }}</td>
                                <td>{{ $buyer->address_1 }}</td>
                                <td>{{ $buyer->address_2 }}</td>
                                <td>{{ $buyer->phone_no }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
        $('#buyersTable').DataTable(); 
    });
</script>
@endpush