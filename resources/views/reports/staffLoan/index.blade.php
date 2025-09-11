@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Reports')
@section('content_header_subtitle', 'Staff Loan Report')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Filter Options</h3>
                </div>
                <form action="{{ route('report.staff.loan.generate') }}" method="GET">
                    <div class="card-body">
                        <div class="row">
                            @php
                            $fromDate = now()->startOfMonth()->format('Y-m-d');
                            $toDate = now()->format('Y-m-d');
                            @endphp
                            <div class="col-md-3">
                                <label>From Date</label>
                                <input type="date" name="from_date" class="form-control" 
                                    value="">
                            </div>
                            <div class="col-md-3">
                                <label>To Date</label>
                                <input type="date" name="to_date" class="form-control" value="">
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
    $('#yahai_id').change(function() {
        const yahaiId = $(this).val();
        $('#membership_id').prop('disabled', true).empty().append(
            '<option value="">Select Saltern</option>');
        if (yahaiId) {
            $.ajax({
                url: "{{ route('get.reports.saltern') }}",
                type: "GET",
                data: {
                    yahai_id: yahaiId
                },
                success: function(response) {
                    response.salterns.forEach(saltern => {
                        if (saltern.memberships && saltern.memberships.length > 0) {
                            saltern.memberships.forEach(membership => {
                                $('#membership_id').append(
                                    `<option value="${membership.id}">${saltern.name} - ${membership.owner?.name_with_initial ?? 'No Owner'}</option>`
                                );
                            });
                        }
                    });
                    $('#membership_id').prop('disabled', false);
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