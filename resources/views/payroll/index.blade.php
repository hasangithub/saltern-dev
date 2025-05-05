@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'payrolls')
@section('content_header_subtitle', 'payrolls')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">payrolls</h3>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <form action="{{ route('payroll.view') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Year</label>
                                <select name="year" class="form-control">
                                    @foreach($years as $y)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Month</label>
                                <select name="month" class="form-control">
                                    @foreach($months as $m)
                                    <option value="{{ $m }}">{{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">View Payroll</button>
                            </div>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('payroll.generate') }}">
                        @csrf
                        <button class="btn btn-success">Generate Current Month Payroll</button>
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
$(document).ready(function() {
    $('#membershipsTable').DataTable();
});
</script>
@endpush