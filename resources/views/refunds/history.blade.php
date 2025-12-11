@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Refunds')
@section('content_header_subtitle', 'Refunds')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container">
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <div class="card">
        <div class="card-body p-4">
            @if($refunds->isEmpty())
            <div class="alert alert-info">No refunds found yet.</div>
            @else
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Membership</th>
                            <th>Total Charge</th>
                            <th>Refund Amount</th>
                            <th>Voucher</th>
                            <th>Created By</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($refunds as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td> <strong>Membership:</strong> {{ $r->memberships->id ?? '-' }} <br>
                                <strong>Saltern:</strong> {{ $r->memberships->saltern->name ?? '-' }} <br>
                                <strong>Yahai:</strong> {{ $r->memberships->saltern->yahai->name ?? '-' }} <br>
                                <strong>Owner:</strong> {{ $r->memberships->owner->name_with_initial ?? '-' }}
                            </td>
                            <td class="text-right">{{ number_format($r->total_service_charge,2) }}</td>
                            <td class="text-right">{{ number_format($r->refund_amount,2) }}</td>
                            <td>
                                @if($r->voucher_id)
                                <a href="{{ route('vouchers.show', $r->voucher_id) }}"
                                    target="_blank">#{{ $r->voucher_id }}</a>
                                @else
                                <span class="text-muted">Not vouchered</span>
                                @endif
                            </td>
                            <td>{{ optional($r->creator)->name ?? $r->created_by }}</td>
                            <td>{{ $r->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('refunds.show', $r->id) }}" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-2">
                {{ $refunds->links() }}
            </div>
            @endif
        </div>
    </div>

    <div class="mt-3">

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