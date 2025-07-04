@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Chart of Accounts')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
<style>
.treeview {
    list-style: none;
    padding-left: 20px;
}

.treeview-item {
    cursor: pointer;
}

.treeview-item i {
    margin-right: 5px;
}

.nested {
    display: none;
}

.open>.nested {
    display: block;
}

.toggle-node {
    cursor: pointer;
    color: #007bff;
}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"></h3>
                    <a href="{{ route('sub-account-groups.create') }}" class="btn btn-success btn-sm"> <i
                            class="fas fa-plus"></i>
                        Sub Accounts</a>
                    <a href="{{ route('ledgers.create') }}" class="btn btn-success btn-sm"> <i class="fas fa-plus"></i>
                        Ledgers</a>
                    <a href="{{ route('sub-ledgers.create') }}" class="btn btn-success btn-sm"> <i
                            class="fas fa-plus"></i>
                        Sub Ledgers</a>
                </div>

                <div class="card-body">
                    <div class="container mt-4">
                        <ul class="treeview">
                            @foreach($accounts as $key => $account)
                            <li class="treeview-item">
                                <i class="fas fa-folder toggle-node"></i> {{ $account->id ." ". $account->name }}
                                @if($account->subAccountGroups->isNotEmpty())
                                @foreach($account->subAccountGroups as $subKey => $subAccount)
                                <ul class="nested">
                                    <li class="treeview-item">
                                        <i class="fas fa-folder toggle-node"></i>
                                        <span class="editable" data-id="{{ $subAccount->id }}" data-type="subaccount"
                                            data-name="{{ $subAccount->name }}">{{ $subAccount->name }}
                                        </span>
                                        @if($subAccount->ledgers->isNotEmpty())
                                        @foreach($subAccount->ledgers as $ledgerKey => $ledger)
                                        <ul class="nested">
                                            <li class="treeview-item">
                                                <i class="fas fa-folder toggle-node"></i>
                                                <span class="editable" data-id="{{ $ledger->id }}" data-type="ledger"
                                                    data-name="{{ $ledger->name }}">{{ $ledger->name }}
                                                </span>
                                                @if($ledger->subLedgers->isNotEmpty())
                                                @foreach($ledger->subLedgers as $subLedgerKey => $subLedger)
                                                <ul class="nested">
                                                    <li class="treeview-item"><i class="fas fa-file"></i>
                                                        <span class="editable" data-id="{{ $subLedger->id }}"
                                                            data-type="subledger"
                                                            data-name="{{ $subLedger->name }}">{{ $subLedger->name }}
                                                        </span>
                                                    </li>
                                                </ul>
                                                @endforeach
                                                @endif
                                            </li>
                                        </ul>
                                        @endforeach
                                        @endif
                                    </li>
                                </ul>
                                @endforeach
                                @endif
                            </li>
                            @endforeach
                        </ul>
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
    $(document).on('click', '.toggle-node', function(e) {
        e.stopPropagation();

        const $item = $(this).closest('.treeview-item');
        $item.toggleClass('open');

        $(this).toggleClass('fa-folder fa-folder-open');
    });

    $(document).on('click', '.editable', function(e) {
        e.stopPropagation(); // Avoid toggle

        const $span = $(this);
        const originalText = $span.text().trim();
        const id = $span.data('id');
        const type = $span.data('type');

        // Replace with input
        const $input = $('<input type="text" class="edit-input">')
            .val(originalText)
            .css({
                width: 'auto',
                minWidth: '150px'
            });

        $span.replaceWith($input);
        $input.focus();

        // Save on blur
        $input.on('blur', function() {
            const newText = $input.val().trim();
            const $newSpan = $('<span class="editable">')
                .attr('data-id', id)
                .attr('data-type', type)
                .text(newText);

            $input.replaceWith($newSpan);

            if (newText !== originalText) {
                // AJAX request to update
                $.ajax({
                    url: '{{ route("account-tree.update") }}',
                    type: 'POST',
                    data: {
                        id: id,
                        type: type,
                        name: newText,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        console.log('Updated successfully');
                    },
                    error: function() {
                        alert('Update failed');
                    }
                });
            }
        });
    });


});
</script>
@endpush