@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Reports')
@section('content_header_subtitle', 'Trial Balance')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
        <div class="container">
    <h3 class="mb-4">Balance Sheet</h3>
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Assets</th>
                <th>Amount</th>
                <th>Equity & Liabilities</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            {{-- Loop over Assets --}}
            @php
                $assetGroups = $balanceSheet['Assets'] ?? [];
                $maxRows = max(count($assetGroups), 4); // 4 right side rows minimum
            @endphp

            @for ($i = 0; $i < $maxRows; $i++)
                <tr>
                    {{-- Left side: Assets --}}
                    <td>
                        @if(isset($assetGroups[$i]))
                            <strong>{{ $assetGroups[$i]['group_name'] }}</strong>
                            <ul>
                                @foreach($assetGroups[$i]['sub_groups'] as $subName => $sub)
                                    <li><strong>{{ $subName }}</strong>
                                        <ul>
                                            @foreach($sub['ledgers'] as $ledger)
                                                <li>{{ $ledger['name'] }} - {{ number_format($ledger['balance'], 2) }}</li>
                                            @endforeach
                                        </ul>
                                        <strong>Sub Total: {{ number_format($sub['total'], 2) }}</strong>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </td>
                    <td>
                        @if(isset($assetGroups[$i]))
                            {{ number_format($assetGroups[$i]['total'], 2) }}
                        @endif
                    </td>

                    {{-- Right side --}}
                    <td>
                        @if($i === 0)
                            Accumulated Fund
                        @elseif($i === 1)
                            Net Profit
                        @elseif($i === 2)
                            Reserve
                        @elseif($i === 3)
                            Accounts Payable
                        @endif
                    </td>
                    <td>
                        @if($i === 0)
                            {{ number_format($accumulatedFund, 2) }}
                        @elseif($i === 1)
                            {{ number_format($netProfit, 2) }}
                        @elseif($i === 2)
                            {{ number_format($reserves, 2) }}
                        @elseif($i === 3)
                            {{ number_format($payables, 2) }}
                        @endif
                    </td>
                </tr>
            @endfor

            <tr class="table-secondary">
                <th>Total Assets</th>
                <th>
                    {{ number_format(collect($assetGroups)->sum('total'), 2) }}
                </th>
                <th>Total Equity + Liabilities</th>
                <th>{{ number_format($equityAndLiabilities, 2) }}</th>
            </tr>
        </tbody>
    </table>
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