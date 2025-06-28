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
                                <i class="fas fa-folder"></i> {{ $account->id ." ". $account->name }}
                                @if($account->subAccountGroups->isNotEmpty())
                                @foreach($account->subAccountGroups as $subKey => $subAccount)
                                <ul class="nested">
                                    <li class="treeview-item">
                                        <i class="fas fa-folder"></i> {{ $subAccount->id . " " .$subAccount->name }}
                                        @if($subAccount->ledgers->isNotEmpty())
                                        @foreach($subAccount->ledgers as $ledgerKey => $ledger)
                                        <ul class="nested">
                                            <li class="treeview-item">
                                                <i class="fas fa-folder"></i> {{ $ledger->id . " " .$ledger->name }}
                                                @if($ledger->subLedgers->isNotEmpty())
                                                @foreach($ledger->subLedgers as $subLedgerKey => $subLedger)
                                                <ul class="nested">
                                                    <li class="treeview-item"><i class="fas fa-file"></i>
                                                        {{$subLedger->id . " " .$subLedger->name}}</li>
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
    $('.treeview-item').click(function(e) {
        e.stopPropagation(); // Prevent triggering parent clicks
        $(this).toggleClass('open');
        $(this).find('i').first().toggleClass('fa-folder fa-folder-open');
    });
});
</script>
@endpush