@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Reports')
@section('content_header_subtitle', 'Trial Balance')
@section('page-buttons')

@endsection
{{-- Content body: main page content --}}

@section('content_body')

<div class="container-fluid">
    <div class="callout callout-info">
        {{ now()->format('Y-m-d H:i:s') }}<br>
        @if(request('from_date') && request('to_date'))
        {{ request('from_date') }} -
        {{ request('to_date') }}
        @endif
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card w-100">
                <div class="card-header">
                    <small class="float-right">
                        Total Amount: <b>Rs. {{ number_format($totalAmount, 2) }}</b>
                    </small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="membershipsTable" class="table table-sm nowrap table-hover w-100">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Receipt No</th>
                                    <th>Owner</th>
                                    <th>Buyer</th>
                                    <th>Entry Type</th>
                                    <th>Description</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($receiptDetails as $row)
                                <tr>
                                    <td>{{ $row->receipt->receipt_date }}</td>
                                    <td>{{ $row->receipt->id }}</td>
                                    <td>
                                        @if ($row['entry_type'] == 'weighbridge')
                                        {{ $row['entry']->owner->name_with_initial ?? '-' }}
                                        @elseif ($row['entry_type'] == 'other_income')
                                        {{ '-' }}
                                        @elseif ($row['entry_type'] == 'loan')
                                        {{ $row['entry']->ownerLoan->membership->owner->name_with_initial ?? '-' }}
                                        @endif
                                    </td>
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
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
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