@extends('layout.app')

@section('subtitle', 'Edit Buyer')
@section('content_header_title', 'Edit Buyer')
@section('content_header_subtitle', 'Update buyer information')

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Edit Buyer</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('buyers.update', $buyer->id) }}" method="POST" autocomplete="off">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">

                                <div class="form-group">
                                    <label for="business_name">Business Name:</label>
                                    <input type="text" name="business_name" id="business_name" class="form-control"
                                        value="{{ old('business_name', $buyer->business_name) }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="business_registration_number">Business Registration Number:</label>
                                    <input type="text" name="business_registration_number"
                                        id="business_registration_number" class="form-control"
                                        value="{{ old('business_registration_number', $buyer->business_registration_number) }}">
                                </div>

                                <div class="form-group">
                                    <label for="full_name">Buyer Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name"
                                        value="{{ old('full_name', $buyer->full_name) }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="credit_limit">Credit Limit</label>
                                    <input type="number" step="0.01" class="form-control" id="credit_limit"
                                        name="credit_limit" value="{{ old('credit_limit', $buyer->credit_limit) }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="service_out">Service Out</label>
                                    <input type="hidden" name="service_out" value="false">
                                    <input type="checkbox" id="service_out" name="service_out" value="true"
                                        {{ old('service_out', $buyer->service_out) ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="col-md-6">

                                <div class="form-group">
                                    <label for="address_1">Address</label>
                                    <input type="text" class="form-control" id="address_1" name="address_1"
                                        value="{{ old('address_1', $buyer->address_1) }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="phone_number">Phone Number</label>
                                    <input type="text" class="form-control" id="phone_number" name="phone_number"
                                        value="{{ old('phone_number', $buyer->phone_number) }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="secondary_phone_number">Secondary Phone Number</label>
                                    <input type="text" name="secondary_phone_number" id="secondary_phone_number"
                                        class="form-control"
                                        value="{{ old('secondary_phone_number', $buyer->secondary_phone_number) }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="whatsapp_number">Whatsapp Number</label>
                                    <input type="text" name="whatsapp_number" id="whatsapp_number" class="form-control"
                                        value="{{ old('whatsapp_number', $buyer->whatsapp_number) }}">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('css')
@endpush

@push('js')
<script>
    console.log("Buyer edit page loaded");
</script>
@endpush
