@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Owners')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">List</h3>
                    <a href="{{ route('private-weighbridge-entries.create') }}" class="btn btn-success ml-auto"> <i
                            class="fas fa-plus"></i>
                        Create First Weight</a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    <div class="table-responsive">
                        <table id="ownersTable" class="table table-sm" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Vehicle</th>
                                    <th>Customer</th>
                                    <th>Buyer</th>
                                    <th>1st Wt</th>
                                    <th>2nd Wt</th>
                                    <th>Net</th>
                                    <th>Amount</th>
                                    <th>Paid</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($entries as $e)
                                <tr>
                                    <td>{{ $e->id }}</td>
                                    <td>{{ $e->transaction_date }}</td>
                                    <td>{{ $e->vehicle_id ?? '-' }}</td>
                                    <td>{{ $e->customer_name ?? '-' }}</td>
                                    <td>{{ $e->buyer->full_name ?? '-' }}</td>
                                    <td>{{ $e->first_weight }}</td>
                                    <td>{{ $e->second_weight ?? '-' }}</td>
                                    <td> @if($e->second_weight > 0)
                                        {{ abs($e->second_weight - $e->first_weight) }}
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $e->amount ?? '-' }}</td>
                                    <td>
                                        @if($e->is_paid)
                                        <span class="badge bg-success">Paid</span>
                                        @else
                                        <span class="badge bg-warning">Unpaid</span>
                                        @endif
                                    </td>
                                    <td>{{ ucfirst($e->status) }}</td>
                                    <td>
                                        @if($e->status === 'pending')
                                        <a href="{{ route('private-weighbridge-entries.edit', $e) }}"
                                            class="btn btn-sm btn-info">
                                            2nd Weight
                                        </a>
                                        @endif
                                        @if($e->status === 'completed')
                                        <a href="{{ route('private_weighbridge_entries.invoice', $e) }}?mode=list" target="_blank"
                                            class="btn btn-sm btn-warning">
                                            Print
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
    $('#ownersTable').DataTable({
        ordering: false, // 🔴 IMPORTANT
    });

    // ---------------------
    // Success session alert & print
    // ---------------------
    @if(session('success'))
    @if(session('print_type') === 'second')
    if (confirm("{{ session('success') }}\n\nDo you want to print the invoice?")) {
        window.open("{{ route('private_weighbridge_entries.invoice', session('print_entry_id')) }}",
            "_blank");
    }
    @endif
    @endif
});
</script>
@endpush