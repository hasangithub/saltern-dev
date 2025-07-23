@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Reports')
@section('content_header_subtitle', 'Trial Balance')

{{-- Content body: main page content --}}

@section('content_body')
<div class="meta">
    @php
    $printedOn = now()->format('Y-m-d H:i:s');
    @endphp
    <div>Printed on: <strong>{{ $printedOn }}</strong></div>
    @if(request('from_date') && request('to_date'))
    <div>Date Range: <strong>{{ request('from_date') }}</strong> to
        <strong>{{ request('to_date') }}</strong>
    </div>
    @endif
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Receipt No</th>
                        <th>Owner</th>
                        <th>Buyer</th>
                        <th>Entry Type</th>
                        <th>Description</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($receiptDetails as $row)
                    <tr>
                        <td>{{ $row->receipt->receipt_date }}</td>
                        <td>{{ $row->receipt->id }}</td>
                        <td> @if ($row['entry_type'] == 'weighbridge')
                            {{ $row['entry']->owner->name_with_initial ?? '-' }}
                            @elseif ($row['entry_type'] == 'other_income')
                            {{ '-' }}
                            @elseif ($row['entry_type'] == 'loan')
                            {{ $row['entry']->ownerLoan->membership->owner->name_with_initial ?? '-' }}
                            @endif</td>
                        <td>{{ $row->receipt->buyer->full_name ?? '-' }}</td>
                        <td>{{ ucfirst($row['entry_type']) }}</td>
                        <td>
                            @if ($row['entry_type'] == 'weighbridge')
                            NetWeight(Kg): {{ $row['entry']->net_weight ?? '-' }}
                            @elseif ($row['entry_type'] == 'other_income')
                            {{ $row['entry']->incomeCategory->name ?? '-' }}
                            @elseif ($row['entry_type'] == 'loan')
                            Loan Id#{{ $row['entry']->ownerLoan->id ?? '-' }}
                            Loan Repayment Id#{{ $row['entry']->id ?? '-' }}
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($row['amount'], 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">No records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @stop

    {{-- Push extra CSS --}}

    @push('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    @endpush

    {{-- Push extra scripts --}}

    @push('js')
    <script>
    $(document).ready(function() {
        $('#membershipsTable').DataTable();
    });
    </script>
    @endpush