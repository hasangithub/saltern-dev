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
            <td><strong>Employee Name:</strong></td>
            <td>{{ $repayment->staffLoan->user->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Department:</strong></td>
            <td>{{ $repayment->staffLoan->user->employee->department ?? '-' }}</td>
        </tr>
         <tr>
            <td><strong>Designation:</strong></td>
            <td>{{ $repayment->staffLoan->user->employee->designation ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Loan ID:</strong></td>
            <td>{{ $repayment->staffLoan->id }}</td>
        </tr>
        <tr>
            <td><strong>Paid Amount:</strong></td>
            <td>Rs. {{ number_format($repayment->amount, 2) }}</td>
        </tr>
        <tr>
            <td colspan="2">
                <strong>Description</strong>
            </td>
        </tr>

        <tr>
            <td colspan="2" style="border:1px solid #ccc; padding:6px;">
                {{ $repayment->notes ?? '-' }}
            </td>
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