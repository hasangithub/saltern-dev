@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Vouchers')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
<div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $approvedCount }}</h3>
                    <p>Approved</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="{{ route('vouchers.index', ['status' => 'approved']) }}" class="small-box-footer">
                    List <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $pendingCount }}</h3>
                    <p>Pending</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="{{ route('vouchers.index', ['status' => 'pending']) }}" class="small-box-footer">
                    List <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card {{ $cardOutline }}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Vouchers</h3>
                    <a href="{{ route('vouchers.create') }}" class="btn btn-success ml-auto"> <i
                            class="fas fa-plus"></i> Create Voucher</a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table id="membershipsTable" class="table table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                    <th>Bank</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($memberships as $membership)
                                <tr>
                                    <td>{{ $membership->created_at }}</td>
                                    <td>{{ $membership->name }}</td>
                                    <td>{{ $membership->amount }}</td>
                                    <td>{{ $membership->description }}</td>
                                    <td> @if ($membership->bank)
                                        {{ $membership->bank->bank_name }}
                                        @else
                                        
                                        @endif
                                    </td>
                                    <td>{{ $membership->status }}</td>
                                    <td><a href="{{ route('vouchers.show', $membership->id) }}"
                                            class="btn btn-default btn-xs">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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