@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Weighbridge')
@section('content_header_subtitle', 'Welcome')
@section('plugins.Datatables', true)

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Owner Details Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Details</h3>
                </div>
                <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Buyer Name</strong>
                            <p class="text-muted">{{ $weighbridgeEntry->buyer->full_name }}</p>
                            <hr>
                            <strong>Date</strong>
                            <p class="text-muted">{{ $weighbridgeEntry->transaction_date }}</p>
                            <hr>
                            <strong>Vehicle No</strong>
                            <p class="text-muted"> {{ $weighbridgeEntry->vehicle_id }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>First Weight</strong>
                            <p class="text-muted"> {{ $weighbridgeEntry->formatted_initial_weight  }}</p>
                            <hr>
                            <strong>Second Weight</strong>
                            <p class="text-muted"> {{ $weighbridgeEntry->formatted_tare_weight }}</p>
                            <hr>
                            <strong>Net Weight</strong>
                            <p class="text-muted"> {{ $weighbridgeEntry->formatted_net_weight }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Bags</strong>
                            <p class="text-muted"> {{ $weighbridgeEntry->bags_count }}</p>
                            <hr>
                            <strong>Price/Bag</strong>
                            <p class="text-muted"> {{ $weighbridgeEntry->bag_price }}</p>
                            <hr>
                            <strong>Total Amount</strong>
                            <p class="text-muted"> {{ $weighbridgeEntry->formatted_total_amount  }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>Owner</strong>
                            <p class="text-muted"> {{ $weighbridgeEntry->owner->full_name }}</p>
                            <hr>
                            <strong>Waikal</strong>
                            <p class="text-muted"> {{ $weighbridgeEntry->membership->saltern->name }}</p>
                            <hr>
                            <strong>Yahai</strong>
                            <p class="text-muted"> {{ $weighbridgeEntry->membership->saltern->yahai->name }}</p>
                        </div>
                    </div>
                    @if (empty($weighbridgeEntry->tare_weight))
                            <button type="button" class="btn btn-warning" data-toggle="modal"
                                data-target="#approveLoanModal">
                                Add Tare Weight
                            </button>
                            @endif
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>

<div class="modal fade" id="approveLoanModal" tabindex="-1" aria-labelledby="approveLoanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('weighbridge_entries.tare', $weighbridgeEntry->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="approveLoanModalLabel">Approve weighbridgeEntry</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="comments" class="form-label">Tare Weight</label>
                        <input type="number" class="form-control" id="tare_weight" name="tare_weight" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
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
    
});
</script>
@endpush