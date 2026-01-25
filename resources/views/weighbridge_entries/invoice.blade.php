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
            <td><strong>Owner:</strong></td>
            <td>{{ $entry->membership->owner->name_with_initial ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Yahai:</strong></td>
            <td>{{ $entry->membership->saltern->yahai->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Waikal:</strong></td>
            <td>{{ $entry->membership->saltern->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Buyer:</strong></td>
            <td>{{ $entry->buyer->full_name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Vehicle No:</strong></td>
            <td>{{ $entry->vehicle_id }}</td>
        </tr>
        <tr>
            <td><strong>Net Weight:</strong></td>
            <td>{{ number_format($entry->net_weight, 2) }} kg</td>
        </tr>
        <tr>
            <td><strong>No. of Bags:</strong></td>
            <td>{{ $entry->bags_count ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Date of Distribution:</strong></td>
            <td>{{ \Carbon\Carbon::parse($entry->transaction_date)->format('Y-m-d') }}</td>
        </tr>
    </table>
</div>
@if ($mode === 'list')
    

<div class="details">
    <p><strong>Service Charge:</strong> <span class="amount-right">Rs.
            {{ number_format($entry->total_amount, 2) }}</span></p>
    @php
    $loanPaid = $totalPaid;
    $totalPayable = $entry->total_amount + $loanPaid;
    @endphp
    @if($loanPaid > 0)

    <p><strong>Loan Paid:</strong> <span class="amount-right">Rs.
            {{ number_format($loanPaid, 2) }}</span></p>
    @endif

    <p> {{ \App\Helpers\NumberToWordHelper::convert($totalPayable ?? 0) }} only.
    </p>
    <p><span class="amount-right">Rs.
            {{ number_format($totalPayable, 2) }}</span></p>
</div>

@endif

<div class="footer">
    <div class="signature">
        <div class="line"></div>
        <p>Cashier</p>
    </div>
</div>
@endsection