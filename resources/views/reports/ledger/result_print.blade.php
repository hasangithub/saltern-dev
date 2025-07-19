<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Subledger Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        h4, p {
            margin: 0 0 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background: #f0f0f0;
        }
        .text-right {
            text-align: right;
        }
        .fw-bold {
            font-weight: bold;
        }
        .bg-light {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>

    <h4>Subledger Report: {{ $subLedger->name }}</h4>
    <p>Ledger: {{ $ledger->name }}</p>
    <p>Period: {{ $fromDate }} to {{ $toDate }}</p>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Credit</th>
                <th class="text-right">Running Balance</th>
            </tr>
        </thead>
        <tbody>
            @php $running = $opening['balance']; @endphp

            <tr class="fw-bold bg-light">
                <td colspan="4">Opening Balance</td>
                <td class="text-right">
                    {{ number_format(abs($running), 2) }} {{ $running >= 0 ? 'Dr' : 'Cr' }}
                </td>
            </tr>

            @foreach($journalDetails as $jd)
            @php $running += $jd->debit_amount - $jd->credit_amount; @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($jd->journalEntry->journal_date)->format('Y-m-d') }}</td>
                <td>{{ $jd->journalEntry->description }}</td>
                <td class="text-right">{{ number_format($jd->debit_amount, 2) }}</td>
                <td class="text-right">{{ number_format($jd->credit_amount, 2) }}</td>
                <td class="text-right">
                    {{ number_format(abs($running), 2) }} {{ $running >= 0 ? 'Dr' : 'Cr' }}
                </td>
            </tr>
            @endforeach

            <tr class="fw-bold">
                <td colspan="2">Total</td>
                <td class="text-right">{{ number_format($journalDetails->sum('debit_amount'), 2) }}</td>
                <td class="text-right">{{ number_format($journalDetails->sum('credit_amount'), 2) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

</body>
</html>
