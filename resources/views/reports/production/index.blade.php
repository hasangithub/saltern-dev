@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Reports')
@section('content_header_subtitle', 'Production Report')

{{-- Content body: main page content --}}

@section('content_body')
@php
$fromDate = now()->startOfMonth()->format('Y-m-d');
$toDate = now()->format('Y-m-d');
@endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">All Production Details</h3>
                </div>
                <form action="{{ route('all-production.report.generate') }}" method="GET">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label>From Date</label>
                                <input type="date" name="from_date" class="form-control" required
                                    value="{{ $fromDate }}">
                            </div>
                            <div class="col-md-3">
                                <label>To Date</label>
                                <input type="date" name="to_date" class="form-control" required value="{{ $toDate }}">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Generate Report</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Owner Production Details</h3>
                </div>
                <form action="{{ route('production.report.generate') }}" method="GET">
                    <div class="card-body">
                        <div class="row">
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
                                <label>Yahai</label>
                                <select name="yahai_id" id="yahai_id" class="form-control" required>
                                    <option value=""></option>
                                    @foreach($yahaies as $yahai)
                                    <option value="{{ $yahai->id }}">{{ $yahai->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Member</label>
                                <select name="membership_id" id="membership_id" class="form-control select2" required>
                                    <option value=""></option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Buyer (optional)</label>
                                <select name="buyer_id" class="form-control">
                                    <option value="">All</option>
                                    @foreach($buyers as $buyer)
                                    <option value="{{ $buyer->id }}">{{ $buyer->full_name }}</option>
                                    @endforeach
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

        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Production Details - Buyer</h3>
                </div>
                <form action="{{ route('production.report.buyerGenerate') }}" method="GET">
                    <div class="card-body">
                        <div class="row">
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
                                <label>Buyer </label>
                                <select name="buyer_id" class="form-control" required>
                                    <option value="">All</option>
                                    @foreach($buyers as $buyer)
                                    <option value="{{ $buyer->id }}">{{ $buyer->full_name }}</option>
                                    @endforeach
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
    $('.select2').select2();
    $('#membershipsTable').DataTable();

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