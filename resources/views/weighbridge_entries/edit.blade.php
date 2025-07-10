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

                    <form method="POST" action="{{ route('weighbridge_entries.update', $entry->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label>Vehicle ID</label>
                            <input type="text" name="vehicle_id" class="form-control" value="{{ $entry->vehicle_id }}"
                                required>
                        </div>

                        <div class="mb-3">
                            <label>Buyer</label>
                            <select name="buyer_id" class="form-control" required>
                                @foreach($buyers as $buyer)
                                <option value="{{ $buyer->id }}" {{ $entry->buyer_id == $buyer->id ? 'selected' : '' }}>
                                    {{ $buyer->full_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Entry</button>
                        <a href="{{ route('weighbridge_entries.index') }}" class="btn btn-secondary">Cancel</a>
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