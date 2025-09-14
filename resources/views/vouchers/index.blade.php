@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Vouchers')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Vouchers</h3>
                    <a href="{{ route('vouchers.create') }}" class="btn btn-success ml-auto"> <i
                            class="fas fa-plus"></i> Create Voucher</a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table id="membershipsTable" class="table table-sm nowrap table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>VId</th>
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
                            <tbody>
                                @foreach($memberships as $membership)
                                <tr>
                                    <td>{{ $membership->id }}</td>
                                    <td>{{ $membership->created_at->format('Y-m-d') }}</td>
                                    <td><span class="d-inline-block text-truncate" style="max-width: 150px;" title="{{ $membership->name }}">{{ $membership->name }}</span></td>
                                    <td>{{ $membership->amount }}</td>
                                    <td>{{ $membership->description }}</td>
                                    <td> @if ($membership->bank_sub_ledger_id)
                                        {{ $membership->bank->name }} / {{ $membership->cheque_no }} /
                                        {{ $membership->cheque_date }}
                                        @else

                                        @endif
                                    </td>
                                    <td>
                                        {{ optional($membership->ledger)->name ?? '-' }} -
                                        {{ optional(optional($membership->ledger)->subLedgers)->name ?? '-' }}
                                    </td>

                                    <td>{{ $membership->status }}</td>
                                    <td>
                                        <a href="{{ route('vouchers.print', $membership->id) }}"
                                            class="btn btn-sm btn-primary" target="_blank">
                                            <i class="fa fa-print"></i> Print
                                        </a>
                                        <a href="{{ route('vouchers.show', $membership->id) }}"
                                            class="btn btn-default btn-xs">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
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
    $('#membershipsTable').DataTable({
        order: [
            [0, 'desc']
        ],
        pageLength: 100,
        autoWidth: true,
    });
});
</script>
@endpush