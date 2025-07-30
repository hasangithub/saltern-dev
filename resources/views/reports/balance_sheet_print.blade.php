@extends('layout.report_a4')
@section('section-title', 'Balance Sheet')
@section('content')

<style>
.print-balance-sheet {
    display: table;
    width: 100%;
    table-layout: fixed;
}

.print-column {
    display: table-cell;
    vertical-align: top;
    width: 50%;
    padding: 0px;
    box-sizing: border-box;
}

.balance-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}

.balance-table th,
.balance-table td {
    border: 1px solid #000;
    padding: 6px;
    /* text-align: left; */
}

.total-row {
    background-color: #e9ecef;
    font-weight: bold;
}
</style>

<div class="print-balance-sheet">
    {{-- Left Column: Assets --}}
    <div class="print-column">
        <table class="balance-table">
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
            <tr class="total-row">
                <td><strong>Total Assets</strong></td>
                <td class="text-right"><strong>{{ number_format($assetsTotal, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    {{-- Right Column: Liabilities & Equity --}}
    <div class="print-column">
        <table class="balance-table">
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
                <td colspan="2"><strong>Current Liabilities</strong></td>
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

            {{-- Pad rows if left side is taller --}}
            @for ($i = 0; $i < $assetRowCount - $rightRowCount; $i++) <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                </tr>
                @endfor

                <tr class="total-row">
                    <td><strong>Total Liabilities & Equity</strong></td>
                    <td class="text-right"><strong>{{ number_format($equityTotal + $liabilitiesTotal, 2) }}</strong>
                    </td>
                </tr>

                {{-- Optional: Difference row --}}
                @php
                $difference = round($assetsTotal - ($equityTotal + $liabilitiesTotal), 2);
                @endphp

                @if ($difference != 0)
                <tr>
                    <td style="border: none;"></td>
                    <td class="text-right" style="border: none;">
                        {{ number_format($difference, 2) }}
                    </td>
                </tr>
                @endif
        </table>
    </div>
</div>
@endsection('content')