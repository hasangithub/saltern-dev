@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Receipt Details')
@section('content_header_subtitle', 'Receipt Details')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Details</h3>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table id="membershipsTable" class="table table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Receipt Detail Id</th>
                                    <th>Receipt Id</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($receiptDetails->details as $receiptDetail)
                                <tr>
                                    <td>{{ $receiptDetail->id }}</td>
                                    <td>{{ $receiptDetail->receipt_id }}</td>
                                    <td>{{ $receiptDetail->entry_type }}</td>
                                    <td>{{ $receiptDetail->amount }}</td>
                                </tr>
                                @endforeach
                            </tbody>
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
    $('#membershipsTable').DataTable();
});
</script>
@endpush