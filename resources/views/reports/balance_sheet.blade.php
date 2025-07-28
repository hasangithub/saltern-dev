@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Reports')
@section('content_header_subtitle', 'Trial Balance')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container">
    <h2>Balance Sheet</h2>
    <a href="{{ route('balance-sheet.print', request()->all()) }}" class="btn btn-primary" target="_blank">
        <i class="fas fa-print"></i> Print Balance Sheet
    </a>
    <div class="row">
        <!-- Left: Assets -->
        <div class="col-md-6">

            @foreach ($data['Assets'] as $group)
            <strong>{{ $group['name'] }}</strong>
            <table class="table">

                @foreach ($group['subGroups'] as $subName => $sub)

                <td><strong>{{ $subName }}</td>

                @foreach ($sub['ledgers'] as $ledger)
                <tr>
                    <td>{{ $ledger['name'] }}</td>
                    <td class="text-right">{{ number_format($ledger['total'], 2) }}</td>
                </tr>

                @endforeach
                <tr colspan="2">
                    <td>Total</td>
                    <td class="text-right">{{ number_format($sub['total'], 2) }}</td>
                </tr>

                </li>

                @endforeach

            </table>
            <hr>
            @endforeach
            <table class="table">
                <tr>
                    <td>Total Assets</td>
                    <td class="text-right"> {{ number_format($assetsTotal, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Right: Equity & Liabilities -->
        <div class="col-md-6">
            <strong>Liabilities and Equity</strong></br>
            <strong>Equity(Capital)</strong>
            <table class="table">
                @foreach ($data['Equity'] as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td class="text-right">{{ number_format($item['total'], 2) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td>Total Equity</td>
                    <td class="text-right">{{ number_format($equityTotal, 2) }}</td>
                </tr>
            </table>
            <hr>
            <strong>Current Liabilities</storng>
                <table class="table">
                    @foreach ($data['CurrentLiabilities'] as $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td class="text-right">{{ number_format($item['total'], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td>Total Liabilities</td>
                        <td class="text-right">{{ number_format($liabilitiesTotal, 2) }}</td>
                    </tr>
                </table>
                <hr>
                <table class="table">
                    <tr>
                        <td>Total Equity & Liabilities</td>
                        <td class="text-right"> {{ number_format($equityTotal + $liabilitiesTotal, 2) }}</td>
                    </tr>
                </table>
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