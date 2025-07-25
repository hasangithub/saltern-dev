@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Journals')
@section('content_header_subtitle', 'Journals')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Journals</h3>
                    <a href="{{ route('journal-entries.create') }}" class="btn btn-success ml-auto"> <i
                            class="fas fa-plus"></i> Create Entry</a>
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
                                    <th>Detail ID</th>
                                    <th>Ledger</th>
                                    <th>Sub Ledger</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($journalDetails->details as $journalDetail)
                                <tr>
                                    <td>{{ $journalDetail->id }}</td>
                                    <td>{{ $journalDetail->ledger->name }}</td>
                                    <td>{{ optional($journalDetail->subLedger)->name }}</td>
                                    <td class="text-right">Rs. {{ number_format($journalDetail->debit_amount, 2) }}</td>
                                    <td class="text-right">Rs. {{ number_format($journalDetail->credit_amount, 2) }}</td>
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