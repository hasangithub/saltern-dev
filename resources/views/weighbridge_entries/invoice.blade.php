<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Weighbridge Invoice</title>
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
        line-height: 1.3;
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
        margin-top: 4px;
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
        <div class="section-title">WEIGHMENT</div>
    </div>

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

    <div class="details">
        <p><strong>Service Charge:</strong> <span class="amount-right">Rs.
                {{ number_format($entry->total_amount, 2) }}</span></p>
        @php
        $loanPaid = $repayment->amount ?? 0;
        $totalPayable = $entry->total_amount + $loanPaid;
        @endphp
        @if($repayment)

        <p><strong>Loan Paid:</strong> <span class="amount-right">Rs.
                {{ number_format($repayment->amount, 2) }}</span></p>
        @endif

        <p> {{ \App\Helpers\NumberToWordHelper::convert($totalPayable ?? 0) }} only.
        </p>
        <p><span class="amount-right">Rs.
                {{ number_format($totalPayable, 2) }}</span></p>
    </div>

    <div class="footer">
        <div class="signature">
            <div class="line"></div>
            <p>Cashier</p>
        </div>
    </div> 
</body>

</html>