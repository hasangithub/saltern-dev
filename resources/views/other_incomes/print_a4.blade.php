<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Other Income Invoice</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            color: #333;
            margin: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header-title {
            font-size: 20px;
            font-weight: bold;
            color: #000;
        }

        .sub-header {
            font-size: 13px;
            color: #555;
        }

        .section-divider {
            margin: 15px 0;
            border-top: 2px solid #444;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #222;
            text-transform: uppercase;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        th, td {
            border: 1px solid #999;
            padding: 10px;
            font-size: 14px;
        }

        th {
            background-color: #f0f0f0;
            color: #000;
            text-align: left;
        }

        .details {
            margin-top: 20px;
            font-size: 14px;
        }

        .details td {
            padding: 4px 8px;
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


        .right-align {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-title">PUTTALAM SALT PRODUCERS WELFARE SOCIETY LTD</div>
        <div class="sub-header">Saltern Yard, Mannar Road, Puttalam</div>
        <div class="sub-header">Tel: 032-2265260 / Email: pspwsl@gmail.com</div>
        <div class="section-divider"></div>
        <div class="section-title">Invoice</div>
    </div>

    <table class="details">
        <tr>
            <td><strong>ID:</strong></td>
            <td>{{ $income->id }}</td>
            <td><strong>Date:</strong></td>
            <td>{{ \Carbon\Carbon::parse($income->received_date)->format('Y-m-d') }}</td>
        </tr>
        <tr>
            <td><strong>Buyer:</strong></td>
            <td colspan="3">{{ $income->buyer->full_name }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 30%;">Category</th>
                <th>Description</th>
                <th style="width: 20%;" class="right-align">Amount (Rs.)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $income->incomeCategory->name }}</td>
                <td>{{ $income->description }}</td>
                <td class="right-align">{{ number_format($income->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="signature">
            <div class="line"></div>
            <p>Cashier</p>
        </div>
    </div>
</body>
</html>
