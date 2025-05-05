@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Loan')
@section('content_header_title', 'SMS')
@section('content_header_subtitle', 'SMS')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
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
                    <!-- Update Settings Form -->
                    <form action="{{ route('sms.settings.update') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="api_key">API Key</label>
                            <input type="text" class="form-control" name="api_key" id="api_key" value="{{ $settings->api_key ?? '' }}"
                                required>
                        </div>
                        
                        <div class="form-group">
                            <label for="sender_id">Sender ID</label>
                            <input type="text" class="form-control" name="sender_id" id="sender_id" value="{{ $settings->sender_id ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="sms_enabled">Enable SMS</label>
                            <input type="checkbox" class="form-check-input" name="sms_enabled" id="sms_enabled" 
                                {{ $settings->sms_enabled ? 'checked' : '' }}>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </form>

                    <!-- Test SMS Form -->
                    <h2>Test SMS</h2>
                    <form action="{{ route('sms.test') }}" method="POST">
                        @csrf
                        <div>
                            <label for="phone_number">Phone Number</label>
                            <input type="text" class="form-control" name="phone_number" id="phone_number" required>
                        </div>
                        <button type="submit">Send Test SMS</button>
                    </form>

                    <!-- Send SMS Form -->
                    <h2>Send SMS</h2>
                    <form action="{{ route('sms.send') }}" method="POST">
                        @csrf
                        <div>
                            <label for="phone_number">Phone Number</label>
                            <input type="text" class="form-control" name="phone_number" id="phone_number" required>
                        </div>
                        <div>
                            <label for="message">Message</label>
                            <textarea name="message" class="form-control" id="message" required></textarea>
                        </div>
                        <button type="submit">Send SMS</button>
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

</script>
@endpush