@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Dashboard')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">


    <div class="row">
        <div class="col-lg-6">
            <div class="card">

            </div>
            <!-- /.card -->

            <div class="card">

            </div>
            <!-- /.card -->
        </div>
        <!-- /.col-md-6 -->
        <div class="col-lg-6">
            <div class="card">

            </div>
            <!-- /.card -->

            <div class="card">

            </div>
        </div>
        <!-- /.col-md-6 -->
    </div>
    <!-- /.row -->
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

@section('scripts')
<script>

</script>
@endsection