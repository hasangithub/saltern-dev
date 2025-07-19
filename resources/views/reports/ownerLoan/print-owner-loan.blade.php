<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Owner Loan Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h4 {
            margin-bottom: 0;
            color: #0d6efd;
        }

        .header {
            margin-bottom: 20px;
        }

        .saltern-block {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .table th,
        .table td {
            border: 1px solid #dee2e6;
            padding: 5px;
            text-align: right;
        }

        .table th {
            background-color: #f8f9fa;
        }

        .table td:first-child,
        .table td:nth-child(2),
        .table th:first-child,
        .table th:nth-child(2) {
            text-align: left;
        }

        .totals-row {
            font-weight: bold;
            background-color: #f1f1f1;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <p><strong>Owner:</strong> {{ $owner->name_with_initial }}</p>
    </div>

    @foreach ($grouped as $saltern => $loans)
        <div class="saltern-block">
            <h4>{{ $saltern }}</h4>

            @foreach ($loans as $loan)
                @php
                    $totalDebit = 0;
                    $totalCredit = 0;
                    $finalBalance = 0;
                @endphp

                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loan['rows'] as $row)
                            @php
                                $totalDebit += $row['debit'] ?? 0;
                                $totalCredit += $row['credit'] ?? 0;
                                $finalBalance = $row['balance'];
                            @endphp
                            <tr>
                                <td>{{ $row['date'] }}</td>
                                <td>{{ $row['description'] }}</td>
                                <td>{{ $row['debit'] ? number_format($row['debit'], 2) : '' }}</td>
                                <td>{{ $row['credit'] ? number_format($row['credit'], 2) : '' }}</td>
                                <td>{{ number_format($row['balance'], 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="totals-row">
                            <td colspan="2">Total</td>
                            <td>{{ number_format($totalDebit, 2) }}</td>
                            <td>{{ number_format($totalCredit, 2) }}</td>
                            <td>{{ number_format($finalBalance, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        </div>
    @endforeach

</body>
</html>
