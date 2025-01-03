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
                                    <th>ID</th>
                                    <th>Ledger</th>
                                    <th>SubLedger</th>
                                    <th>Debit Amount</th>
                                    <th>Credit Amount</th>
                                    <th>Description</th>
                                    <th>Journal Id</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($journalDetails as $journalDetail)
                                <tr>
                                    <td>{{ $journalDetail->id }}</td>
                                    <td>{{ $journalDetail->subLedger->ledger->name }}</td>
                                    <td>{{ $journalDetail->subLedger->name }}</td>
                                    <td>{{ $journalDetail->debit_amount }}</td>
                                    <td>{{ $journalDetail->credit_amount }}</td>
                                    <td>{{ $journalDetail->description }}</td>
                                    <td>{{ $journalDetail->journal_id }}</td>
                                    <td>
                                        <!-- <a href="{{ route('owner-loans.show', $journalDetail->id) }}"
                                            class="btn btn-default btn-xs">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="{{ route('loan-repayments.create-for-loan', $journalDetail->id) }}"
                                            class="btn btn-primary btn-xs">
                                            + Repayment
                                        </a> -->
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
    $('#membershipsTable').DataTable();
});
</script>
@endpush