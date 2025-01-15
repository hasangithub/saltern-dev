@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Create Buyer')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Create Buyer</h3>
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
                    <form action="{{ route('buyers.store') }}" method="POST" autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">

                                <div class="form-group">
                                    <label for="business_name">Business Name:</label>
                                    <input type="text" name="business_name" id="business_name" class="form-control"
                                        value="{{ old('business_name', $buyer->business_name ?? '') }}">
                                </div>

                                <div class="form-group">
                                    <label for="business_registration_number">Business Registration Number:</label>
                                    <input type="text" name="business_registration_number"
                                        id="business_registration_number" class="form-control"
                                        value="{{ old('business_registration_number', $buyer->business_registration_number ?? '') }}">
                                </div>

                                <div class="form-group">
                                    <label for="full_name">Contact Full Name</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name"
                                        placeholder="">
                                </div>

                                <div class="form-group">
                                    <label for="credit_limit">Credit Limit</label>
                                    <input type="number" step="0.01" class="form-control" id="credit_limit"
                                        name="credit_limit" required>
                                </div>

                                <div class="form-group">
                                    <label for="service_out">Service Out</label>
                                    <input type="checkbox" id="service_out" name="service_out">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address_1">Address</label>
                                    <input type="text" class="form-control" id="address_1" name="address_1" required>
                                </div>

                                <div class="form-group">
                                    <label for="phone_number">Phone Number</label>
                                    <input type="text" class="form-control" id="phone_number" name="phone_number"
                                        required>
                                </div>

                               
                                <div class="form-group">
                                    <label for="secondary_phone_number">Secondary Phone Number</label>
                                    <input type="text" name="secondary_phone_number" id="secondary_phone_number"
                                        class="form-control"
                                        value="{{ old('secondary_phone_number') }}">
                                </div>

                                <div class="form-group">
                                    <label for="whatsapp_number">Whatsapp Number</label>
                                    <input type="text" name="whatsapp_number" id="whatsapp_number" class="form-control"
                                        value="{{ old('whatsapp_number') }}" required>
                                </div>


                            </div>

                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save
                        </button>
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