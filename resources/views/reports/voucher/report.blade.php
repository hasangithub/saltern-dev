@extends('layout.report_a4')
@section('section-title', 'Owner Loan Yahai wise')
@section('content')

<div style="text-align: right; font-weight: bold; margin-bottom: 10px;">
    Total: {{ number_format($totalAmount, 2) }}
</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Voucher Name</th>
            <th>Payment Method</th>
            <th>Bank</th>
            <th>Cheque No</th>
            <th>Cheque Date</th>
            <th>Amount</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($vouchers as $index => $voucher)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $voucher->name }}</td>
            <td>{{ $voucher->paymentMethod->name ?? '-' }}</td>
            <td>{{ $voucher->bank->name ?? '-' }}</td>
            <td>{{ $voucher->cheque_no }}</td>
            <td>{{ $voucher->cheque_date }}</td>
            <td style="text-align: right;">{{ number_format($voucher->amount, 2) }}</td>
            <td>{{ ucfirst($voucher->status) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="6" style="text-align: right;">Total:</th>
            <th style="text-align: right;">{{ number_format($totalAmount, 2) }}</th>
            <th></th>
        </tr>
    </tfoot>
</table>
@endsection