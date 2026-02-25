@extends('layout.report_a4')
@section('section-title', 'Owner Loan Yahai wise')
@section('content')

@section('custom-css')
.table {
width: 100%;
table-layout: fixed;
border-collapse: collapse;
}

.table th,
.table td {
padding: 5px;
overflow: hidden;
white-space: nowrap;
}

/* Fix column widths */
.table th:nth-child(1),
.table td:nth-child(1) {
width: 15%;
}

.table th:nth-child(2),
.table td:nth-child(2) {
width: 40%;
}

.table th:nth-child(3),
.table td:nth-child(3) {
width: 15%;
}
.table th:nth-child(4),
.table td:nth-child(4) {
width: 15%;
}
.table th:nth-child(5),
.table td:nth-child(5) {
width: 15%;
}

.totals-row {
font-weight: bold;
background-color: #f1f1f1;
}

@endsection


<div class="header">
    <p><strong>Owner:</strong> {{ $owner->name_with_initial }}</p>
    <p class="text-right"><strong>Total Outstanding: {{ number_format($totalOutstanding, 2)  }}</strong> </p>
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
                <td class="text-right">{{ $row['debit'] ? number_format($row['debit'], 2) : '' }}</td>
                <td class="text-right">{{ $row['credit'] ? number_format($row['credit'], 2) : '' }}</td>
                <td class="text-right">{{ number_format($row['balance'], 2) }}</td>
            </tr>
            @endforeach
            <tr class="totals-row">
                <td></td>
                <td>Total</td>
                <td class="text-right">{{ number_format($totalDebit, 2) }}</td>
                <td class="text-right">{{ number_format($totalCredit, 2) }}</td>
                <td class="text-right">{{ number_format($finalBalance, 2) }}</td>
            </tr>
        </tbody>
    </table>
    @endforeach
</div>
@endforeach
@endsection