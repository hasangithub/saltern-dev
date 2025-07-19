<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Other Income Receipt</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @media print {
            @page {
                size: A6 portrait;
                margin: 0;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11px;
            line-height: 1.3;
            margin: 0;
        }

        .center {
            text-align: center;
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
        }

        .header-title {
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .sub-header {
            font-size: 11px;
            color: #333;
            margin-top: 2px;
        }

        .section-divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .section-title {
            font-size: 12px;
            text-decoration: underline;
        }

        .details {
            margin-bottom: 1px;
        }

        .details table {
            width: 100%;
            border-collapse: collapse;
        }

        .details td {
            padding: 1px 3px;
            vertical-align: top;
            font-size: 14px;
        }

        .footer {
            margin-top: 16px;
        }

        .signature {
            float: right;
            text-align: center;
            margin-top: 15px;
        }

        .line {
            border-top: 1px solid #000;
            margin-top: 20px;
            width: 100px;
        }

        .amount-right {
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>

<body @if(!isset($from_pdf)) onload="window.print()" @endif>
    <div class="header">
        <div class="header-title">PUTTALAM SALT PRODUCERS WELFARE SOCIETY LTD</div>
        <div class="sub-header">Reg No: S/6709 &nbsp;&nbsp;|&nbsp;&nbsp; Tel/Fax: 032 2265260</div>
        <div class="section-divider"></div>
        <div class="section-title">OTHER INCOME</div>
    </div>

    <div class="details">
        <table>
            <tr>
                <td><strong>OtherIncome Id:</strong></td>
                <td>{{ $income->id }}</td>
            </tr>
            <tr>
                <td><strong>Date:</strong></td>
                <td>{{ \Carbon\Carbon::parse($income->received_date)->format('Y-m-d') }}</td>
            </tr>
            <tr>
                <td><strong>Buyer:</strong></td>
                <td>{{ $income->buyer->full_name }}</td>
            </tr>
            <tr>
                <td><strong>Category:</strong></td>
                <td>{{ $income->incomeCategory->name }}</td>
            </tr>
            <tr>
                <td><strong>Description:</strong></td>
                <td>{{ $income->description }}</td>
            </tr>
            <tr>
                <td><strong>Amount:</strong></td>
                <td>Rs. {{ number_format($income->amount, 2) }}</td>
            </tr>
        </table>
    </div>
</body>

</html>
