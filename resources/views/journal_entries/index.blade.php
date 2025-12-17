@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Journals')
@section('content_header_subtitle', 'Journals')
@section('page-buttons')
<a href="{{ route('journal-entries.create') }}" class="btn btn-success ml-auto"> <i
                            class="fas fa-plus"></i> Create Entry</a>
@endsection
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
                    <div class="table-responsive">
                        <table id="membershipsTable" class="table table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Journal ID</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($journalEntries as $journalEntry)
                                <tr>
                                    <td>{{ $journalEntry->id }}</td>
                                    <td>{{ $journalEntry->journal_date }}</td>
                                    <td>{{ $journalEntry->description }}</td>
                                    <td class="text-end">{{ number_format($journalEntry->total_debit, 2) }}</td>
                                    <td>
                                        <a href="{{ route('journal-entries.show', $journalEntry->id) }}"
                                            class="btn btn-default btn-xs">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        @role('admin')
                                        <form action="{{ route('journal-entries.destroy', $journalEntry->id) }}"
                                            method="POST" style="display:inline;"
                                            onsubmit="return confirm('Are you sure you want to delete this entry?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </form>
                                        @endrole
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
    $('#membershipsTable').DataTable({
        order: [
            [0, 'desc']
        ],
        pageLength: 100
    });
});

</script>
@endpush