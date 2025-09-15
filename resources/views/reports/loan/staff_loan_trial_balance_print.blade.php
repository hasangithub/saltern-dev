@extends('layout.report_a4')
@section('section-title', ' Staff Loan Trail Balance')
@section('content')
<h4 class="mb-0 text-primary">
    Grand Total: Rs. {{ number_format($grandTotal, 2) }}
</h4>
@foreach ($grouped as $yahai => $records)
<h4 class="mt-4">{{ $records[0]['owner'] ?? 'No staff found' }}</h4>
<table>
    <thead>
        <tr>
            <th>Loan ID</th>
            <th class="text-right">Approved</th>
            <th class="text-right">Repaid</th>
            <th class="text-right">Outstanding</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($records as $row)
        <tr>
            <td>{{ $row['loan_id'] }}</td>
            <td class="text-right">{{ number_format($row['approved'], 2) }}</td>
            <td class="text-right">{{ number_format($row['repaid'], 2) }}</td>
            <td class="text-right">{{ number_format($row['outstanding'], 2) }}</td>
        </tr>
        @endforeach
        <tr class="fw-bold">
            <td colspan="3" class="text-right">Total Outstanding ({{ $row['owner'] }})</td>
            <td class="text-right">{{ number_format($yahaiTotals[$yahai], 2) }}</td>
        </tr>
    </tbody>
</table>
@endforeach
@endsection('content')