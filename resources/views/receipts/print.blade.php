@extends('layout.print_a6')
@section('section-title', '')
@section('content')
<div class="details">
    <table width="100%">
        <tr>
            <td align="left">
                <p style="margin: 0;">Receipt #{{ $receipt->id }}</p>
            </td>
            <td align="right">
                <p style="margin: 0;">Date: {{ $receipt->created_at->format('d-m-Y') }}</p>
            </td>
        </tr>
    </table>
    <p><strong>Buyer:</strong> {{ $receipt->buyer->full_name ?? 'N/A' }}</p>
    <table width="100%" border="1" cellspacing="0" cellpadding="4">
        <thead>
            <tr>
                <th>Entry Type</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach ($receipt->details as $detail)
            <tr>
                <td> 
                @if ($detail->entry_type === 'other_income')
                    {{ $detail->entry?->incomeCategory?->name }} #{{ $detail->entry_id }} 
                    ({{ $detail->entry?->description }})
                    {{ $detail->owner?->name_with_initial }}
                @else
                    {{ ucfirst($detail->entry_type) }}#{{ $detail->entry_id }} 
                    {{ $detail->owner?->name_with_initial }}
                @endif</td>
                <td align="right">{{ number_format($detail->amount, 2) }}</td>
            </tr>
            @php $total += $detail->amount; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td><strong>Total</strong></td>
                <td align="right"><strong>{{ number_format($total, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>
</div>
<div class="footer">
    <div class="signature">
        <div class="line"></div>
        <p>Cashier</p>
    </div>
</div>
@endsection