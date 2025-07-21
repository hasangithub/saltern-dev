@extends('layout.report_a4')
@section('section-title', ' Trial Balance')
@section('content')


    <table>
        <thead>
            <tr>
                <th>Sub Group</th>
                <th>Ledger / Subledger</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Credit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($trialData as $row)
            <tr>
                <td class="text-left">{{ $row['sub_group'] }}</td>
                <td class="text-left">
                    @if (!empty($row['is_sub']))
                    <span style="margin-left: 20px;">{{ $row['ledger'] }}</span>
                    @else
                    <strong>{{ $row['ledger'] }}</strong>
                    @endif
                </td>

                <td class="text-right">{{ number_format($row['debit'], 2) }}</td>
                <td class="text-right">{{ number_format($row['credit'], 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" class="text-right">Total</td>
                <td class="text-right">{{ number_format($totalDebit, 2) }}</td>
                <td class="text-right">{{ number_format($totalCredit, 2) }}</td>
            </tr>
        </tbody>
    </table>
@endsection('content')