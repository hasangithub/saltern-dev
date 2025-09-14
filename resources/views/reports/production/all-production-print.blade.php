@extends('layout.report_a4')
@section('section-title', ' All Production Details')
@section('content')

    @if(isset($entries) && $entries->count())
    <table>
        <thead>
            <tr>
                <th style="width: 60px;">Date</th>
                <th>Owner Name</th>
                <th>Waikal</th>
                <th>Buyer Name</th>
                <th style="width: 30px;">Culture</th>
                <th class="text-right" style="width: 30px;">Net Weight (kg)</th>
                <th class="text-right" style="width: 30px;">Bags</th>
                <th class="text-right" style="width: 30px;">Tons</th>
                <th class="text-right" style="width: 50px;">Service Charge 30%</th>
            </tr>
        </thead>
        <tbody>
            @php
            $totalNetWeight = 0;
            $totalBags = 0;
            $totalServiceCharge30 = 0;
            @endphp
            @foreach($entries as $entry)
            @php
            $totalNetWeight += $entry->net_weight;
            $totalBags += $entry->bags_count;
            $serviceCharge30 = round($entry->total_amount * ($entry->owner_share_percentage / 100), 2);
            $totalServiceCharge30 += $serviceCharge30;
            @endphp
            <tr>
                <td>{{ $entry->transaction_date }}</td>
                <td>{{ $entry->membership->owner->name_with_initial ?? '-' }}</td>
                <td>{{ $entry->membership->saltern->yahai->name .'-'. $entry->membership->saltern->name}}</td>
                <td>{{ $entry->buyer->full_name ?? '-' }}</td>
                <td>{{ $entry->culture }}</td>
                <td class="text-right">{{ number_format($entry->net_weight, 2) }}</td>
                <td class="text-right">{{ $entry->bags_count }}</td>
                <td class="text-right">{{ number_format($entry->net_weight / 1000, 2) }}</td>
                <td class="text-right">{{ number_format($serviceCharge30, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5">Total</th>
                <th class="text-right">{{ number_format($totalNetWeight, 2) }}</th>
                <th class="text-right">{{ $totalBags }}</th>
                <th class="text-right">{{ number_format($totalNetWeight / 1000, 2) }}</th>
                <th class="text-right">{{ number_format($totalServiceCharge30, 2) }}</th>
            </tr>
        </tfoot>
    </table>
    @endif

    @endsection('content')