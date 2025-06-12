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
            <form action="{{ route('production.report.generate') }}" method="GET">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label>From Date</label>
                            <input type="date" name="from_date" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label>To Date</label>
                            <input type="date" name="to_date" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label>Yahai</label>
                            <select name="yahai_id" class="form-control" required>
                                <option value=""></option>
                                @foreach($yahaies as $yahai)
                                <option value="{{ $yahai->id }}">{{ $yahai->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Member</label>
                            <select name="owner_id" class="form-control" required>
                                <option value=""></option>
                                @foreach($owners as $owner)
                                <option value="{{ $owner->id }}">{{ @$owner->membership_no." ".$owner->owner->name_with_initial }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Buyer (optional)</label>
                            <select name="buyer_id" class="form-control">
                                <option value="">All</option>
                                @foreach($buyers as $buyer)
                                <option value="{{ $buyer->id }}">{{ $buyer->business_name }}</option>
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
    $('#membershipsTable').DataTable();
});
</script>
@endpush