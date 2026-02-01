@extends('layout.report_a4')

@section('section-title', 'Annual Production Report')

@section('content')

@if(!empty($report))
@php
        // Assuming all entries are for the same owner
        $firstEntry = $report->first();
        $firstMembership = $firstEntry?->membership;
        $owner = $firstEntry?->membership?->owner ?? $firstEntry?->owner ?? null;
        $saltern = $firstMembership?->saltern;
        $yahai = $saltern?->yahai;
        @endphp
        <div class="row g-1">
            <div class="col-md-4 col-sm-6">
                <strong>Owner:</strong> {{ $owner->name_with_initial ?? 'N/A' }}
            </div>
            <div class="col-md-4 col-sm-6">
                <strong>Yahai:</strong> {{ $yahai?->name ?? 'N/A' }}
            </div>
            <div class="col-md-4 col-sm-6">
                <strong>Saltern:</strong> {{ $saltern?->name ?? 'N/A' }}
            </div>
            <div class="col-md-4 col-sm-6">
                <strong>Membership No:</strong> {{ $firstMembership?->membership_no ?? 'N/A' }}
            </div>
            <div class="col-md-4 col-sm-6">
                <strong>From:</strong> {{ request('from_date') }}
            </div>
            <div class="col-md-4 col-sm-6">
                <strong>To:</strong> {{ request('to_date') }}
            </div>
            <div class="col-md-4 col-sm-6">
                <strong>Printed:</strong> {{ \Carbon\Carbon::now()->format('Y-m-d') }}
            </div>
        </div>
<table>
    <thead>
        <tr>
            <th style="width: 80px;">Year</th>
            <th class="text-right">Ag Salt (Bags)</th>
            <th class="text-right">Yala (Bags)</th>
            <th class="text-right">Maha (Bags)</th>
            <th class="text-right">Total (Bags)</th>
        </tr>
    </thead>

    <tbody>
        @php
            $grandAg = 0;
            $grandYala = 0;
            $grandMaha = 0;
            $grandTotal = 0;
        @endphp

        @foreach($report as $year => $row)
            @php
                $grandAg += $row['ag_salt'];
                $grandYala += $row['yala'];
                $grandMaha += $row['maha'];
                $grandTotal += $row['total'];
            @endphp
            <tr>
                <td>{{ $row['year'] }}</td>
                <td class="text-right">{{ number_format($row['ag_salt']) }}</td>
                <td class="text-right">{{ number_format($row['yala']) }}</td>
                <td class="text-right">{{ number_format($row['maha']) }}</td>
                <td class="text-right"><strong>{{ number_format($row['total']) }}</strong></td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th>Grand Total</th>
            <th class="text-right">{{ number_format($grandAg) }}</th>
            <th class="text-right">{{ number_format($grandYala) }}</th>
            <th class="text-right">{{ number_format($grandMaha) }}</th>
            <th class="text-right">{{ number_format($grandTotal) }}</th>
        </tr>
    </tfoot>
</table>
@endif

@endsection
