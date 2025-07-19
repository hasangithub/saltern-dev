<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Voucher Report</title>
    <style>
    body {
        font-family: "Times New Roman", serif;
        font-size: 12px;
        margin: 25px;
    }

    h2,
    h4 {
        text-align: center;
        margin: 0;
        padding: 0;
    }

    h4 {
        margin-bottom: 20px;
    }

    .sub-header {
        text-align: center;
        margin-bottom: 20px;
        font-size: 12px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 6px;
    }

    th {
        background-color: #f0f0f0;
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .text-left {
        text-align: left;
    }

    .total-row {
        font-weight: bold;
        background-color: #eaeaea;
    }
    </style>
</head>
<body>

<h2>PUTTALAM SALT PRODUCERS WELFARE SOCIETY LTD</h2>
    <div class="sub-header">
        <strong>Voucher Report</strong><br>
        Printed on: {{ now()->format('Y-m-d H:i') }}
        @if(request('from_date') && request('to_date'))
        <div>Date Range: <strong>{{ request('from_date') }}</strong> to <strong>{{ request('to_date') }}</strong></div>
        @endif
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

</body>
</html>
