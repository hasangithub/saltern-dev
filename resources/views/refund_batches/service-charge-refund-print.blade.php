@extends('layout.a4_landscape')
@section('section-title', ' Service Charge Refund')
@section('content')

@foreach($grouped as $yahaiId => $salterns)
    @php
        $firstRefund = $salterns->first()->first();
        $yahaiName = optional($firstRefund->memberships->saltern->yahai)->name ?? 'Unknown Yahai';
        $batchName = optional($firstRefund->refund_batch)->name ?? '';
    @endphp

    <h3>Yahai: {{ $yahaiName }}</h3>
    <h3>Batch: {{ $batchName }}</h3>

    <table>
        <thead>
            <tr>
                <th style="width:20px;">Saltern</th>
                <th style="width:40px;">Owner</th>
                <th style="width:30px;">Voucher ID</th>
                <th style="width:40px;">Bank</th>
                <th style="width:30px;">Chq No</th>
                <th style="width:20px;" class="text-right">30% Amount</th>
                <th style="width:40px;" class="text-right">Signature</th>
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
                    <td class="text-right">{{ number_format($refund->refund_amount,2) }}</td>
                    <td></td>
                </tr>
            @endforeach
        @endforeach

        {{-- ONLY Yahai Total (no saltern subtotal) --}}
        <tr class="yahai-total">
            <td colspan="5"><strong>Total for {{ $yahaiName }}</strong></td>
            <td class="text-right">
                <strong>{{ number_format($salterns->flatten()->sum('refund_amount'),2) }}</strong>
            </td>
            <td></td>
        </tr>

        </tbody>
    </table>

    <div class="page-break"></div>
@endforeach

@endsection('content')