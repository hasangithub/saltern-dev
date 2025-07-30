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
            <td><strong>Buyer:</strong></td>
            <td>{{ $repayment->buyer->full_name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Owner:</strong></td>
            <td>{{ $repayment->ownerLoan->membership->owner->name_with_initial ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Yahai:</strong></td>
            <td>{{ $repayment->ownerLoan->membership->saltern->yahai->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Waikal:</strong></td>
            <td>{{ $repayment->ownerLoan->membership->saltern->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Loan ID:</strong></td>
            <td>{{ $repayment->ownerLoan->id }}</td>
        </tr>
        <tr>
            <td><strong>Paid Amount:</strong></td>
            <td>Rs. {{ number_format($repayment->amount, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Outstanding Amount:</strong></td>
            <td>Rs. {{ number_format($outstandingAmount, 2) }}</td>
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