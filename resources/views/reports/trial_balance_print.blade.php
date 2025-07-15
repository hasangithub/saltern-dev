<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Trial Balance Report</title>
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
        <strong>Trial Balance Report</strong><br>
        Printed on: {{ now()->format('Y-m-d H:i') }}
        @if(request('from_date') && request('to_date'))
        <div>Date Range: <strong>{{ request('from_date') }}</strong> to <strong>{{ request('to_date') }}</strong></div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Sub Group</th>
                <th>Ledger / Subledger</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Credit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($trialData as $row)
            <tr>
                <td class="text-left">{{ $row['sub_group'] }}</td>
                <td class="text-left">
                    @if (!empty($row['is_sub']))
                    <span style="margin-left: 20px;">{{ $row['ledger'] }}</span>
                    @else
                    <strong>{{ $row['ledger'] }}</strong>
                    @endif
                </td>

                <td class="text-right">{{ number_format($row['debit'], 2) }}</td>
                <td class="text-right">{{ number_format($row['credit'], 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" class="text-right">Total</td>
                <td class="text-right">{{ number_format($totalDebit, 2) }}</td>
                <td class="text-right">{{ number_format($totalCredit, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>