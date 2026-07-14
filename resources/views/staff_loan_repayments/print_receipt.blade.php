@extends('layout.print_a6')
@section('section-title', 'Loan Repayment')
@section('content')

<div class="details">
    <table>
        <tr>
            <td><strong>Loan Repayment Id:</strong></td>
            <td>{{ $repayment->id }}</td>
        </tr>
        <tr>
            <td><strong>Date:</strong></td>
            <td>{{ \Carbon\Carbon::parse($repayment->repayment_date)->format('Y-m-d') }}</td>
        </tr>
        <tr>
            <td><strong>Owner:</strong></td>
            <td>{{ $repayment->staffLoan->user->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Loan ID:</strong></td>
            <td>{{ $repayment->staffLoan->id }}</td>
        </tr>
        <tr>
            <td><strong>Paid Amount:</strong></td>
            <td>Rs. {{ number_format($repayment->amount, 2) }}</td>
        </tr>
    </table>
</div>

<div class="footer">
    <div class="signature">
        <div class="line"></div>
        <p>Cashier</p>
    </div>
</div>
@endsection