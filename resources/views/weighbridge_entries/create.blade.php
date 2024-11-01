@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Weighbridge Entry')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Create new entry</h3>
                </div>

                <div class="card-body">
                    <form action="{{ route('weighbridge_entries.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vehicle_id">Vehicle ID</label>
                                    <input type="text" name="vehicle_id" id="vehicle_id" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="initial_weight">Initial Weight</label>
                                    <input type="number" step="0.01" name="initial_weight" id="initial_weight"
                                        class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="tare_weight">Tare Weight</label>
                                    <input type="number" step="0.01" name="tare_weight" id="tare_weight"
                                        class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="transaction_date">Transaction Date</label>
                                    <input type="date" name="transaction_date" id="transaction_date"
                                        class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="owner_id">Owner</label>
                                    <select name="owner_id" id="owner_id" class="form-control" required>
                                        <option value="">Select Owner</option>
                                        @foreach($owners as $owner)
                                        <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="buyer_id">Buyer</label>
                                    <select name="buyer_id" id="buyer_id" class="form-control" required>
                                        <option value="">Select Buyer</option>
                                        @foreach($buyers as $buyer)
                                        <option value="{{ $buyer->id }}">{{ $buyer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>


                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
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
console.log("Hi, I'm using the Laravel-AdminLTE package!");
</script>
@endpush