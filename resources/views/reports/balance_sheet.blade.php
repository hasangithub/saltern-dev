@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Reports')
@section('content_header_subtitle', 'Trial Balance')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container">
    <h2>Balance Sheet</h2>
    <div class="row">
        <!-- Left: Assets -->
        <div class="col-md-6">
            <h4>Assets</h4>
            @foreach ($data['Assets'] as $group)
                <strong>{{ $group['name'] }}</strong>
                <ul>
                    @foreach ($group['subGroups'] as $subName => $sub)
                        <li><strong>{{ $subName }}</strong>
                            <ul>
                                @foreach ($sub['ledgers'] as $ledger)
                                    <li>{{ $ledger['name'] }} - {{ number_format($ledger['total'], 2) }}</li>
                                @endforeach
                            </ul>
                            <strong>Total: {{ number_format($sub['total'], 2) }}</strong>
                        </li>
                    @endforeach
                </ul>
                <strong>Group Total: {{ number_format($group['total'], 2) }}</strong>
                <hr>
            @endforeach
            <h5><strong>Grand Total Assets: {{ number_format($assetsTotal, 2) }}</strong></h5>
        </div>

        <!-- Right: Equity & Liabilities -->
        <div class="col-md-6">
            <h4>Equity</h4>
            @foreach ($data['Equity'] as $item)
                <p>{{ $item['name'] }} - {{ number_format($item['total'], 2) }}</p>
            @endforeach
            <strong>Total Equity: {{ number_format($equityTotal, 2) }}</strong>

            <hr>
            <h4>Current Liabilities</h4>
            @foreach ($data['CurrentLiabilities'] as $item)
                <p>{{ $item['name'] }} - {{ number_format($item['total'], 2) }}</p>
            @endforeach
            <strong>Total Liabilities: {{ number_format($liabilitiesTotal, 2) }}</strong>

            <hr>
            <h5><strong>Total Equity & Liabilities: {{ number_format($equityTotal + $liabilitiesTotal, 2) }}</strong></h5>
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

