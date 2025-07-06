@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Reports')
@section('content_header_subtitle', 'Production Report')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Filter Options</h3>
                </div>
                <form action="{{ route('ledger.report.generate') }}" method="GET">
                    <div class="card-body">
                        <div class="row">
                            @php
                            $fromDate = now()->startOfMonth()->format('Y-m-d');
                            $toDate = now()->format('Y-m-d');
                            @endphp
                            <div class="col-md-3">
                                <label>From Date</label>
                                <input type="date" name="from_date" class="form-control" required
                                    value="{{ $fromDate }}">
                            </div>
                            <div class="col-md-3">
                                <label>To Date</label>
                                <input type="date" name="to_date" class="form-control" required value="{{ $toDate }}">
                            </div>
                            <div class="col-md-2">
                                <label>Sub Accounts</label>
                                <select name="sub_account_id" id="sub_account_id" class="form-control" required>
                                    <option value=""></option>
                                    @foreach($subAccountGroups as $subAccountGroup)
                                    <option value="{{ $subAccountGroup->id }}">{{ $subAccountGroup->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Ledgers</label>
                                <select name="ledger_id" id="ledger_id" class="form-control select2" required>
                                    <option value=""></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Sub Ledgers</label>
                                <select name="sub_ledger_id" id="sub_ledger_id" class="form-control select2">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Generate Report</button>
                    </div>
                </form>
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
    $('#sub_account_id').change(function() {
        const subAccountId = $(this).val();
        $('#ledger_id').prop('disabled', true).empty().append(
            '<option value="">Select Ledgers</option>');
        if (subAccountId) {
            $.ajax({
                url: "{{ route('get.reports.ledgers') }}",
                type: "GET",
                data: {
                    sub_account_id: subAccountId
                },
                success: function(response) {
                    response.ledgers.forEach(ledger => {
                        $('#ledger_id').append(
                            `<option value="${ledger.id}">${ledger.id}${ledger.name}</option>`
                        );
                    });
                    $('#ledger_id').prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching salterns :', error);
                }
            });
        }
    });

    
    $('#ledger_id').change(function() {
        const ledger_id = $(this).val();
        $('#sub_ledger_id').prop('disabled', true).empty().append(
            '<option value="">Select Sub Ledger</option>');
       
        if (ledger_id) {
            $.ajax({
                url: "{{ route('get.reports.subledgers') }}",
                type: "GET",
                data: {
                    ledger_id: ledger_id
                },
                success: function(response) {
                    response.subLedgers.forEach(subLedger => {
                        $('#sub_ledger_id').append(
                            `<option value="${subLedger.id}">${subLedger.name}</option>`
                        );
                    });
                    $('#sub_ledger_id').prop('disabled', false);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching salterns :', error);
                }
            });
        }
    });
});
</script>
@endpush