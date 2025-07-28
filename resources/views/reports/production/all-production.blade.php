@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Reports')
@section('content_header_subtitle', 'Production Report')

{{-- Content body: main page content --}}

@section('content_body')
<style>
@media print {
    body * {
        visibility: hidden;
    }

    #printable-area,
    #printable-area * {
        visibility: visible;
    }

    #printable-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    button {
        display: none;
    }
}
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Production Report</h3>
                    <a href="{{ route('all-production.report.print', request()->all()) }}" class="btn btn-primary"
                        target="_blank">
                        <i class="fas fa-print"></i> Print 
                    </a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div id="printable-area">
                        @if(isset($entries) && $entries->count() > 0)
                        <div class="card card-success  p-2 mb-2">
                            <div class="card-body table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Owner Name</th>
                                            <th>Waikal</th>
                                            <th>Buyer Name</th>
                                            <th>Culture</th>
                                            <th class="text-right">Net Weight (kg)</th>
                                            <th class="text-right">Bags</th>
                                            <th class="text-right">Tons</th>
                                            <th class="text-right">Service Charge 30%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $totalNetWeight = 0;
                                        $totalBags = 0;
                                        $totalAmount = 0;
                                        $totalServiceCharge30 = 0;
                                        @endphp
                                        @foreach($entries as $entry)
                                        @php
                                        $totalNetWeight += $entry->net_weight;
                                        $totalBags += $entry->bags_count;
                                        $totalAmount += $entry->total_amount;
                                        $serviceCharge30 = round($entry->total_amount *
                                        ($entry->owner_share_percentage/100), 2);
                                        $totalServiceCharge30 += $serviceCharge30;
                                        @endphp
                                        <tr>
                                            <td>{{ $entry->transaction_date }}</td>
                                            <td>{{ $entry->membership->owner->name_with_initial ?? '-' }}</td>
                                            <td>{{ $entry->membership->saltern->yahai->name .'-'. $entry->membership->saltern->name}}</td>
                                            <td>{{ $entry->buyer->full_name ?? '-' }}</td>
                                            <td>{{ $entry->culture}}</td>
                                            
                                            <td class="text-right">{{ number_format($entry->net_weight, 2) }}</td>
                                            <td class="text-right">{{ $entry->bags_count }}</td>
                                            <td class="text-right">{{ number_format($entry->net_weight / 1000, 2) }}
                                            </td>
                                            <td class="text-right">{{ number_format($serviceCharge30, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="5">Total</th>
                                            <th class="text-right">{{ number_format($totalNetWeight, 2) }}</th>
                                            <th class="text-right">{{ $totalBags }}</th>
                                            <th class="text-right">{{ number_format($totalNetWeight / 1000, 2) }}</th>
                                            <th class="text-right">{{ number_format($totalServiceCharge30, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>
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