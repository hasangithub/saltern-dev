@extends('layout.report_a4')
@section('section-title', 'inventory Report')
@section('content')

<div style="text-align: right; font-weight: bold; margin-bottom: 10px;">
    Total: {{ number_format($totalAmount, 2) }}
</div>
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Name</th>
            <th>Stock Code</th>
            <th>Qty</th>
            <th>Place</th>
            <th>Amount</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($vouchers as $index => $voucher)
        <tr>
            <td>{{ $voucher->created_at->format('Y-m-d') }}</td>
            <td>{{ $voucher->name }}</td>
            <td>{{ $voucher->stock_code }}</td>
            <td>{{ $voucher->qty }}</td>
            <td>{{ $voucher->place->name}}
            </td>
            <td class="text-right">{{ number_format($voucher->amount, 2) }}</td>
            <td>{{ ucfirst($voucher->status) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5" style="text-align: right;">Total:</th>
            <th style="text-align: right;">{{ number_format($totalAmount, 2) }}</th>
            <th></th>
        </tr>
    </tfoot>
</table>
@endsection