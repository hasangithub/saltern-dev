@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Reports')
@section('content_header_subtitle', 'Balance Sheet')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container">

    <style>
    .balance-sheet-container {
        display: flex;
    }

    .balance-sheet-column {
        width: 50%;
        border-collapse: collapse;
    }

    .balance-table {
        width: 100%;
        border-collapse: collapse;
    }

    .balance-table td,
    .balance-table th {
        border: 1px solid #000;
        padding: 6px;
    }

    .balance-table .total-row {
        background-color: #e9ecef;
        font-weight: bold;
    }
    </style>

    <div class="card card-default">
        <div class="card-header d-flex justify-content-between align-items-right">
            <h5 class="mb-0">Balance Sheet</h5>
            <a href="{{ route('balance-sheet.print', request()->all()) }}" class="btn btn-primary" target="_blank">
                <i class="fas fa-print"></i> Print Balance Sheet
            </a>
        </div>
        <div class="card-body">
            <div class="balance-sheet-container">
                {{-- Left Column: Assets --}}
                <div class="balance-sheet-column">
                    <table class="table balance-table">
                        @php $assetRowCount = 0; @endphp
                        @foreach ($data['Assets'] as $group)
                        <tr>
                            <th colspan="2">{{ $group['name'] }}</th>
                        </tr>
                        @foreach ($group['subGroups'] as $subName => $sub)
                        <tr>
                            <td colspan="2"><strong>{{ $subName }}</strong></td>
                        </tr>
                        @foreach ($sub['ledgers'] as $ledger)
                        <tr>
                            <td>{{ $ledger['name'] }}</td>
                            <td class="text-right">{{ number_format($ledger['total'], 2) }}</td>
                        </tr>
                        @php $assetRowCount++; @endphp
                        @endforeach
                        <tr class="total-row">
                            <td>Total {{ $subName }}</td>
                            <td class="text-right">{{ number_format($sub['total'], 2) }}</td>
                        </tr>
                        @php $assetRowCount++; @endphp
                        @endforeach
                        @endforeach
                        <tr style="border-top: 2px solid #000;">
                            <td  class="text-left"><strong>Total Assets</strong></td>
                            <td  class="text-right"><strong>{{ number_format($assetsTotal, 2) }}</strong>
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- Right Column: Liabilities & Equity --}}
                <div class="balance-sheet-column">
                    <table class="table balance-table">
                        @php $rightRowCount = 0; @endphp
                        <tr>
                            <th colspan="2">Liabilities and Equity</th>
                        </tr>
                        <tr>
                            <th colspan="2">Equity (Capital)</th>
                        </tr>
                        @foreach ($data['Equity'] as $item)
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td class="text-right">{{ number_format($item['total'], 2) }}</td>
                        </tr>
                        @php $rightRowCount++; @endphp
                        @endforeach
                        <tr class="total-row">
                            <td>Total Equity</td>
                            <td class="text-right">{{ number_format($equityTotal, 2) }}</td>
                        </tr>
                        @php $rightRowCount++; @endphp

                        <tr>
                            <th colspan="2">Current Liabilities</th>
                        </tr>
                        @foreach ($data['CurrentLiabilities'] as $item)
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td class="text-right">{{ number_format($item['total'], 2) }}</td>
                        </tr>
                        @php $rightRowCount++; @endphp
                        @endforeach
                        <tr class="total-row">
                            <td>Total Liabilities</td>
                            <td class="text-right">{{ number_format($liabilitiesTotal, 2) }}</td>
                        </tr>
                        @php $rightRowCount++; @endphp

                        {{-- Padding to match left side rows --}}
                        @for ($i = 0; $i < $assetRowCount - $rightRowCount; $i++) <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            </tr>
                            @endfor
                            <tr>
                                <td class="text-left"><strong>Total Liabilities & Equity</strong></td>
                                <td class="text-right">
                                    <strong>{{ number_format($equityTotal + $liabilitiesTotal, 2) }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td style="border: none;"></td>
                                <td class="text-right" style="border: none;"><strong>
                                        {{ number_format($assetsTotal - ($equityTotal + $liabilitiesTotal), 2) }}
                                    </strong></td>
                            </tr>
                    </table>
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