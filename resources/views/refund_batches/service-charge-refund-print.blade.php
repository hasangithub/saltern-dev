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
                <th>Bank</th>
                <th>Chq No</th>
                <th class="text-right">30% Amount</th>
                <th class="text-right">Signature</th>
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
                    <td>{{ optional($refund->memberships->owner)->name_with_initial ?? '-' }}</td>
                    <td>{{ $refund->voucher_id ?? '-' }}</td>
                    <td>{{ $refund->voucher->bank->name ?? '-' }}</td>
                    <td>{{ $refund->voucher->cheque_no ?? '-' }}</td>
                    <td class="text-right">{{ number_format($refund->total_amount,2) }}</td>
                    <td></td>
                </tr>
            @endforeach
        @endforeach

        {{-- ONLY Yahai Total (no saltern subtotal) --}}
        <tr class="yahai-total">
            <td colspan="5"><strong>Total for {{ $yahaiName }}</strong></td>
            <td class="text-right">
                <strong>{{ number_format($salterns->flatten()->sum('total_amount'),2) }}</strong>
            </td>
            <td></td>
        </tr>

        </tbody>
    </table>

    <div class="page-break"></div>
@endforeach

@endsection('content')