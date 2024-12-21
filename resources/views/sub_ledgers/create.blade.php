@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Create Sub Ledger')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title">Create Sub Ledger</h3>
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
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <form action="{{ route('sub-ledgers.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="ledger_id">Ledger</label>
                <select name="ledger_id" id="ledger_id" class="form-control" required>
                    <option value="">Select Ledger</option>
                    @foreach($ledgers  as $ledger)
                    <option value="{{ $ledger->id }}">{{ $ledger->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Sub Ledger Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="">
                    </div>
                </div>

            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save
            </button>
        </form>
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

@endpush