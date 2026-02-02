@extends('layout.print_a6')
@section('section-title', 'WEIGHMENT')
@section('content')


<div class="details">
    <table>
        <tr>
            <td><strong>Rec No:</strong></td>
            <td>{{ $entry->id }}</td>
        </tr>
        <tr>
            <td><strong>First Weight Datetime:</strong></td>
            <td>{{ \Carbon\Carbon::parse($entry->transaction_date . ' ' . $entry->first_weight_time)->format('Y-m-d h:i A') }}</td>
        </tr>
        @if($entry->second_weight > 0)
        <tr>
            <td><strong>Second Weight Datetime:</strong></td>
            <td>{{ \Carbon\Carbon::parse($entry->transaction_date . ' ' . $entry->second_weight_time)->format('Y-m-d h:i A') }}</td>
        </tr>
        @endif
        <tr>
            <td><strong>Vehicle No:</strong></td>
            <td>{{ $entry->vehicle_id }}</td>
        </tr>
        <tr>
            <td><strong>Customer:</strong></td>
            <td>{{ $entry->customer_name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Buyer:</strong></td>
            <td>{{ $entry->buyer->full_name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>First Weight:</strong></td>
            <td>{{ number_format($entry->first_weight, 2) }} kg</td>
        </tr>
        @if($entry->second_weight > 0)
        <tr>
            <td><strong>Second Weight:</strong></td>
            <td>{{ number_format($entry->second_weight, 2) }} kg</td>
        </tr>
        <tr>
            <td><strong>Net Weight:</strong></td>
            <td>{{ number_format($entry->second_weight - $entry->first_weight, 2) }} kg</td>
        </tr>
        @endif
    </table>
</div>
@if ($mode === 'list')
<div class="footer">
    <div class="signature">
        <div class="line"></div>
        <p>Cashier</p>
    </div>
</div>
@endif
@endsection