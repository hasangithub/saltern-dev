@extends('layout.legal')
@section('section-title', ' Service Charge Refund')
@section('content')

@foreach($grouped as $yahaiId => $salterns)
    @php
        $firstRefund = $salterns->first()->first();
        $yahaiName = optional($firstRefund->memberships->saltern->yahai)->name ?? 'Unknown Yahai';
    @endphp

    <h3>Yahai: {{ $yahaiName }}</h3>

    <table>
        <thead>
            <tr>
                <th>Saltern</th>
                <th>Owner</th>
                <th>Voucher ID</th>
                <th class="text-right">Refund Amount</th>
            </tr>
        </thead>
        <tbody>

        @foreach($salterns as $salternId => $refunds)
            @php
                $salternName = optional($refunds->first()->memberships->saltern)->name ?? 'Unknown Saltern';
            @endphp

            @foreach($refunds as $refund)
                <tr>
                    <td>{{ $salternName }}</td>
                    <td>{{ optional($refund->memberships->owner)->name ?? '-' }}</td>
                    <td>{{ $refund->voucher_id ?? '-' }}</td>
                    <td class="text-right">{{ number_format($refund->total_amount,2) }}</td>
                </tr>
            @endforeach
        @endforeach

        {{-- ONLY Yahai Total (no saltern subtotal) --}}
        <tr class="yahai-total">
            <td colspan="3"><strong>Total for {{ $yahaiName }}</strong></td>
            <td class="text-right">
                <strong>{{ number_format($salterns->flatten()->sum('total_amount'),2) }}</strong>
            </td>
        </tr>

        </tbody>
    </table>

    <div class="page-break"></div>
@endforeach

<p style="font-weight:bold; text-align:right;">
    Grand Total: {{ number_format($batch->serviceChargeRefunds->sum('total_amount'),2) }}
</p>

@endsection('content')