<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Loan Trial Balance Report</title>
    <style>
    body {
        font-family: "DejaVu Sans", sans-serif;
        font-size: 12px;
        margin: 30px;
    }

    h2,
    h4 {
        text-align: center;
        margin: 0;
    }

    .sub-header {
        text-align: center;
        margin-bottom: 20px;
        font-size: 12px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 25px;
    }

    th,
    td {
        border: 1px solid #333;
        padding: 5px 8px;
    }

    th {
        background-color: #f0f0f0;
    }

    .text-right {
        text-align: right;
    }

    .fw-bold {
        font-weight: bold;
    }

    .mt-4 {
        margin-top: 20px;
    }
    </style>
</head>

<body>
    <h2>PUTTALAM SALT PRODUCERS WELFARE SOCIETY LTD</h2>
    <div class="sub-header">
        <strong>Loan Trial Balance Report</strong><br>
        Printed on: {{ now()->format('Y-m-d H:i') }}
        @if(request('from_date') && request('to_date'))
        <div>Date Range: <strong>{{ request('from_date') }}</strong> to <strong>{{ request('to_date') }}</strong></div>
        @endif
    </div>
    <h5 class="mb-0 text-primary">
                        Grand Total: Rs. {{ number_format($grandTotal, 2) }}
                    </h5>
    @foreach ($grouped as $yahai => $records)
    <h4 class="mt-4">{{ $yahai }}</h4>
    <table>
        <thead>
            <tr>
                <th>Saltern</th>
                <th>Owner</th>
                <th>Loan ID</th>
                <th class="text-right">Approved</th>
                <th class="text-right">Repaid</th>
                <th class="text-right">Outstanding</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $row)
            <tr>
                <td>{{ $row['saltern'] }}</td>
                <td>{{ $row['owner'] }}</td>
                <td>{{ $row['loan_id'] }}</td>
                <td class="text-right">{{ number_format($row['approved'], 2) }}</td>
                <td class="text-right">{{ number_format($row['repaid'], 2) }}</td>
                <td class="text-right">{{ number_format($row['outstanding'], 2) }}</td>
            </tr>
            @endforeach
            <tr class="fw-bold">
                <td colspan="5" class="text-right">Total Outstanding ({{ $yahai }})</td>
                <td class="text-right">{{ number_format($yahaiTotals[$yahai], 2) }}</td>
            </tr>
        </tbody>
    </table>
    @endforeach
</body>

</html>