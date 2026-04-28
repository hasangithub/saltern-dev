@extends('layout.report_a4')
@section('section-title', 'Buyer Production Details - ' . ($buyerName ?? 'All Buyers'))
@section('content')

@if(isset($entries) && $entries->count())
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Vehicle</th>
            <th>Owner</th>
            <th>Saltern</th>
            <th>Culture</th>
            <th class="text-right">Net Weight (kg)</th>
            <th class="text-right">Bags</th>
            <th class="text-right">Tons</th>
            <th class="text-right">Service Charges</th>
        </tr>
    </thead>
    <tbody>
        @php
        $totalNetWeight = 0;
        $totalBags = 0;
        $totalAmount = 0;

        @endphp
        @foreach($entries as $entry)
        @php
        $totalNetWeight += $entry->net_weight;
        $totalBags += $entry->bags_count;
        $totalAmount += $entry->total_amount;

        @endphp
        <tr>
            <td>{{ $entry->transaction_date }}</td>
            <td>{{ $entry->vehicle_id}}</td>
            <td>{{ $entry->owner->name_with_initial }}</td>
            <td>{{ $entry->membership->saltern->yahai->name . '-' . $entry->membership->saltern->name }}</td>
            <td>{{ $entry->culture}}</td>
            <td class="text-right">{{ number_format($entry->net_weight, 2) }}</td>
            <td class="text-right">{{ $entry->bags_count }}</td>
            <td class="text-right">{{ number_format($entry->net_weight / 1000, 2) }}
            <td class="text-right">{{ number_format($entry->total_amount, 2) }}
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5">Total</th>
            <th class="text-right">{{ number_format($totalNetWeight, 2) }}</th>
            <th class="text-right">{{ $totalBags }}</th>
            <th class="text-right">{{ number_format($totalNetWeight / 1000, 2) }}</th>
            <th class="text-right">{{ number_format($totalAmount, 2) }}</th>
        </tr>
    </tfoot>
</table>
@endif

@endsection('content')