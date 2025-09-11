@extends('layout.report_a4')
@section('section-title', 'Staff Loan User wise')
@section('content')

@section('custom-css')
    .table td:first-child,
    .table td:nth-child(2),
    .table th:first-child,
    .table th:nth-child(2) {
    text-align: right;
    }

    .totals-row {
    font-weight: bold;
    background-color: #f1f1f1;
    }

@endsection


    <div class="header">
        <p><strong>Staff:</strong> </p>
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
                    <td colspan="2">Total</td>
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