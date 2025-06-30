<!DOCTYPE html>
<html>

<head>
    <title>Weighbridge Invoice</title>
    <style>
    body {
        font-family: "Arial", sans-serif;
        margin: 50px;
        font-size: 14px;
    }

    .center {
        text-align: center;
    }

    .header-title {
        font-size: 18px;
        font-weight: bold;
    }

    .sub-header {
        margin-top: -5px;
        font-size: 13px;
    }

    .section-title {
        text-decoration: underline;
        font-weight: bold;
        margin-top: 30px;
        margin-bottom: 20px;
    }

    .details {
        margin-bottom: 20px;
    }

    .details table {
        width: 100%;
        border-collapse: collapse;
    }

    .details td {
        padding: 6px 4px;
        vertical-align: top;
    }

    .footer {
        margin-top: 60px;
    }

    .signature {
        float: right;
        text-align: center;
        margin-top: 50px;
    }

    .line {
        border-top: 1px solid #000;
        margin-top: 40px;
        width: 200px;
    }
    </style>
</head>

<body onload="window.print()">
    <div class="center">
        <div class="header-title">PUTTALAM SALT PRODUCERS WELFARE SOCIETY LTD</div>
        <div class="sub-header">Reg No: S/6709&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tel/Fax: 032 2265260</div>
        <div class="section-title">Invoice for Weighment</div>
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
        <p><strong>Service Charge:</strong> Rs. {{ number_format($entry->total_amount, 2) }}</p>
        <p><strong>In Words:</strong> {{ \App\Helpers\NumberToWordHelper::convert($entry->total_amount ?? 0) }} only.</p>

    </div>

    <div class="footer">
        <div class="signature">
            <div class="line"></div>
            <p>Cashier</p>
        </div>
    </div>

    <script>
    window.onafterprint = function() {
        window.location.href = "{{ route('weighbridge_entries.create') }}";
    };
    </script>
</body>

</html>