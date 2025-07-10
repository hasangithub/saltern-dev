<!DOCTYPE html>
<html>
<head>
    <title>Loan Approval Form</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; margin: 30px; }
        table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #000; }
        h2, h4 { text-align: center; }
        .print-btn { display: none; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align: right; margin-bottom: 10px;">
    <button onclick="window.print()">🖨️ Print</button>
</div>

<h2>Saltern Welfare Society</h2>
<h4>Loan Approval Form</h4>

<table>
    <tr>
        <th>Owner Name</th>
        <td>{{ $loan->membership->owner->name_with_initial }}</td>
    </tr>
    <tr>
        <th>Yahai</th>
        <td>{{ $loan->membership->saltern->yahai->name }}</td>
    </tr>
    <tr>
        <th>Waikal</th>
        <td>{{ $loan->membership->saltern->name }}</td>
    </tr>
    <tr>
        <th>Requested Amount</th>
        <td>Rs. {{ number_format($loan->requested_amount, 2) }}</td>
    </tr>
    <tr>
        <th>Requested Date</th>
        <td>{{ $loan->created_at->format('Y-m-d') }}</td>
    </tr>
    <tr>
        <th>Approved Amount</th>
        <td>
            @if($loan->approved_amount)
                Rs. {{ number_format($loan->approved_amount, 2) }}
            @else
                _______________________
            @endif
        </td>
    </tr>
    <tr>
        <th>Purpose</th>
        <td>{{ $loan->purpose ?? '_______________________' }}</td>
    </tr>
</table>

<p><strong>Remarks / Conditions:</strong></p>
<p>______________________________________________________________________________</p>
<p>______________________________________________________________________________</p>

<table style="margin-top: 50px;">
    <tr>
        <td style="text-align: center;">.................................<br>Manager</td>
        <td style="text-align: center;">.................................<br>Chairman</td>
    </tr>
</table>

</body>
</html>
