@extends('layout.print_a6')
@section('section-title', 'OtherIncome')
@section('content')
<div class="details">
    <table>
        <tr>
            <td><strong>OtherIncome Id:</strong></td>
            <td>{{ $income->id }}</td>
        </tr>
        <tr>
            <td><strong>Date:</strong></td>
            <td>{{ \Carbon\Carbon::parse($income->received_date)->format('Y-m-d') }}</td>
        </tr>
        <tr>
            <td><strong>Buyer:</strong></td>
            <td>{{ $income->buyer->full_name }}</td>
        </tr>
        <tr>
            <td><strong>Category:</strong></td>
            <td>{{ $income->incomeCategory->name }}</td>
        </tr>
        <tr>
            <td><strong>Description:</strong></td>
            <td>{{ $income->description }}</td>
        </tr>
        <tr>
            <td><strong>Amount:</strong></td>
            <td>Rs. {{ number_format($income->amount, 2) }}</td>
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