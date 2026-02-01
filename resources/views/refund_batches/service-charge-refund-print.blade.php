@extends('layout.legal')
@section('section-title', ' Service Charge Refund')
@section('content')

@foreach($grouped as $yahaiId => $salterns)
@php
$firstRefund = $salterns->first()->first();
$yahaiName = optional($firstRefund->memberships->saltern->yahai)->name ?? 'Unknown Yahai';
@endphp

<h3>Yahai: {{ $yahaiName }}</h3>

@foreach($salterns as $salternId => $refunds)
@php
$salternName = optional($refunds->first()->memberships->saltern)->name ?? 'Unknown Saltern';
@endphp

<table>
    <thead>
        <tr>
            <th>Owner</th>
            <th>Waikkal No</th>
            <th>Voucher ID</th>
            <th>Refund Amount</th>
        </tr>
    </thead>
    <tbody>

        @foreach($refunds as $refund)
        <tr>
            <td>{{ optional($refund->memberships->owner)->name_with_initial ?? '-' }}</td>
            <td>{{ $refund->memberships->saltern->name ?? '-' }}</td>
            <td>{{ $refund->voucher_id ?? '-' }}</td>
            <td class="text-right">{{ number_format($refund->refund_amount,2) }}</td>
        </tr>
        @endforeach

        <tr class="subtotal">
            <td colspan="3">Subtotal - {{ $salternName }}</td>
            <td class="text-right">{{ number_format($refunds->sum('refund_amount'),2) }}</td>
        </tr>

        @endforeach
    </tbody>
</table>
@endforeach
@endsection('content')