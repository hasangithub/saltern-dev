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
                                    <td class="text-right">Rs. {{ number_format($journalDetail->credit_amount, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" style="text-align:right">Total:</th>
                                    <th class="text-right"></th>
                                    <th class="text-right"></th>
                                </tr>
                            </tfoot>
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
        "footerCallback": function(row, data, start, end, display) {
            var api = this.api();

            // Helper function to parse string to float (remove Rs. and commas)
            var parseValue = function(i) {
                if (typeof i === 'string') {
                    return parseFloat(i.replace(/Rs\.\s?|,/g, '')) || 0;
                }
                if (typeof i === 'number') {
                    return i;
                }
                return 0;
            };

            // Total over all pages for Debit column (index 3)
            var totalDebit = api
                .column(3)
                .data()
                .reduce(function(a, b) {
                    return parseValue(a) + parseValue(b);
                }, 0);

            // Total over all pages for Credit column (index 4)
            var totalCredit = api
                .column(4)
                .data()
                .reduce(function(a, b) {
                    return parseValue(a) + parseValue(b);
                }, 0);

            // Update footer with formatted totals
            $(api.column(3).footer()).html('Rs. ' + totalDebit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $(api.column(4).footer()).html('Rs. ' + totalCredit.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        }
    });
});
</script>
@endpush