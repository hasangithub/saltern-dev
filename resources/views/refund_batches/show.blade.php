@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Owners')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">

    {{-- Batch Summary --}}
    <div class="card mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h4>{{ $batch->name }}</h4>
                <small>
                    {{ $batch->date_from }} → {{ $batch->date_to }} |
                    {{ $batch->refund_percentage }}%
                </small>
            </div>

            <span class="badge badge-lg badge-{{ 
                $batch->status == 'draft' ? 'warning' :
                ($batch->status == 'approved' ? 'info' :
                ($batch->status == 'posted' ? 'success' : 'danger')) }}">
                {{ strtoupper($batch->status) }}
            </span>
        </div>
    </div>

    {{-- Load Service Charges --}}
    @if($batch->status == 'draft')
    <div class="mb-3">
        {{-- 1. Load remains a link (GET is fine for viewing/fetching) --}}
        <a href="{{ route('refund-batches.load', $batch->id) }}" class="btn btn-outline-primary shadow-sm">
            <i class="fas fa-sync mr-1"></i> Load Service Charges
        </a>

        {{-- 2. Approve MUST be a Form for POST --}}
        @if($batch->status == 'draft')
        <form action="{{ route('refund-batches.approve', $batch->id) }}" method="POST" class="float-right">
            @csrf
            <button type="submit" class="btn btn-success shadow-sm"
                onclick="return confirm('Approve this batch? After approval, amounts cannot be changed.')">
                <i class="fas fa-check-double mr-1"></i> Approve Batch
            </button>
        </form>
        @endif
    </div>
    @endif

    {{-- Membership Service Charges --}}
    <div class="card">
        <div class="card-header">
            Membership Refund Preview
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Membership</th>
                        <th>Service Charge</th>
                        <th>Refund</th>
                        <th>Voucher</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($refunds as $refund)
                    <tr>
                        <td>{{ $refund->memberships->membership_no }}</td>
                        <td class="text-right">{{ number_format($refund->total_service_charge,2) }}</td>
                        <td class="text-right text-danger">
                            {{ number_format($refund->refund_amount,2) }}
                        </td>
                        <td>
                            @if($refund->voucher_id)
                            <span class="badge badge-success">Posted</span>
                            @else
                            <span class="badge badge-warning">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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

</script>
@endpush