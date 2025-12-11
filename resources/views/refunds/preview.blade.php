@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Refunds')
@section('content_header_subtitle', 'Refunds')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container">
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <div class="card">
        <div class="card-body p-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Refund Preview ({{ $from }} → {{ $to }})</h3>
                    <a href="{{ route('refunds.index') }}" class="btn btn-secondary btn-sm">Back</a>
                </div>

                <div class="card-body">


                    @if($grouped->isEmpty())
                    <div class="alert alert-warning">No eligible entries found for the selected date range.</div>
                    @else
                    <form method="POST" action="{{ route('refunds.approve') }}">
                        @csrf
                        <input type="hidden" name="from_date" value="{{ $from }}">
                        <input type="hidden" name="to_date" value="{{ $to }}">

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th style="width:30px"><input type="checkbox" id="check-all" checked></th>
                                        <th>Membership ID</th>
                                        <th class="text-right">Total Service Charge</th>
                                        <th class="text-right">Refund ({{ $refundPercentage }}%)</th>
                                        <th class="text-center"># Entries</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($grouped as $g)
                                    @php
                                    $refundAmount = round(($g->total_service_charge * $refundPercentage)/100, 2);
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="membership_ids[]"
                                                value="{{ $g->membership_id }}" checked>
                                        </td>
                                        <td> {{ $g->membership->id ?? '-' }} |
                                            {{ $g->membership->saltern->name ?? '-' }} |
                                            {{ $g->membership->saltern->yahai->name ?? '-' }} |
                                            {{ $g->membership->owner->name_with_initial ?? '-' }}</td>
                                        <td class="text-right">{{ number_format($g->total_service_charge, 2) }}</td>
                                        <td class="text-right">{{ number_format($refundAmount, 2) }}</td>
                                        <td class="text-center">{{ $g->count }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-success"
                                onclick="return confirm('Approve refunds for selected memberships?')">Approve Selected
                                Refunds</button>
                            <a href="{{ route('refunds.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                    @endif
                </div>
            </div>



        </div>
    </div>

    <div class="mt-3">

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

    document.getElementById('check-all')?.addEventListener('change', function(e) {
        document.querySelectorAll('input[name=\"membership_ids[]\"]').forEach(function(el) {
            el.checked = e.target.checked;
        });
    });
});
</script>
@endpush