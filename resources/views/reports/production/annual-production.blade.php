@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Reports')
@section('content_header_subtitle', 'Annual Production Report')
@section('page-buttons')
<a href="{{ route('annual-production.report.print', request()->all()) }}" class="btn btn-primary" target="_blank">
    <i class="fas fa-print"></i> Print
</a>
@endsection
{{-- Content body: main page content --}}

@section('content_body')

<div class="container-fluid">
    <div class="callout callout-info">
      
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Annual Production Report</h3>

                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="card card-success  p-2 mb-2">
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Year</th>
                                        <th>Ag Salt</th>
                                        <th>Yala</th>
                                        <th>Maha</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($report as $row)
                                    <tr>
                                        <td>{{ $row->year }}</td>
                                        <td>{{ number_format($row->ag_salte) }}</td>
                                        <td>{{ number_format($row->yala) }}</td>
                                        <td>{{ number_format($row->maha) }}</td>
                                        <td class="font-weight-bold">{{ number_format($row->total) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            No production data available
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
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