@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Reports')
@section('content_header_subtitle', 'Production Report')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Production Report</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    @if(isset($entries) && $entries->count() > 0)
                    <div class="card card-success  p-2 mb-2">
                        @php
                        // Assuming all entries are for the same owner
                        $firstEntry = $entries->first();
                        $firstMembership = $firstEntry?->membership;
                        $owner = $firstEntry?->membership?->owner ?? $firstEntry?->owner ?? null;
                        $saltern = $firstMembership?->saltern;
                        $yahai = $saltern?->yahai;
                        @endphp
                        <div class="row g-1">
                            <div class="col-md-4 col-sm-6">
                                <strong>Owner:</strong> {{ $owner->full_name ?? 'N/A' }}
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
                        <div class="card-body table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Buyer Name</th>
                                        <th>Culture</th>
                                        <th>Net Weight (kg)</th>
                                        <th>Bags</th>
                                        <th>Tons</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $totalNetWeight = 0;
                                    $totalBags = 0;
                                    @endphp
                                    @foreach($entries as $entry)
                                    @php
                                    $totalNetWeight += $entry->net_weight;
                                    $totalBags += $entry->bags_count;
                                    @endphp
                                    <tr>
                                        <td>{{ $entry->transaction_date }}</td>
                                        <td>{{ $entry->buyer->business_name ?? '-' }}</td>
                                        <td>{{ $entry->culture}}</td>
                                        <td class="text-right">{{ number_format($entry->net_weight, 2) }}</td>
                                        <td class="text-right">{{ $entry->bags_count }}</td>
                                        <td class="text-right">{{ number_format($entry->net_weight / 1000, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3">Total</th>
                                        <th class="text-right">{{ number_format($totalNetWeight, 2) }}</th>
                                        <th class="text-right">{{ $totalBags }}</th>
                                        <th class="text-right">{{ number_format($totalNetWeight / 1000, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
                <!-- /.card-body -->
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