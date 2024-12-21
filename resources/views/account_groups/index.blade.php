@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'Chart of Accounts')
@section('content_header_subtitle', 'Welcome')

{{-- Content body: main page content --}}

@section('content_body')
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
                    <a href="{{ route('sub-ledgers.create') }}" class="btn btn-success btn-sm"> <i class="fas fa-plus"></i>
                        Sub Ledgers</a>
                </div>

                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($accounts as $key => $account)
                        <li class="list-group-item">
                            <strong>{{ ++$key ." ". $account->name }}</strong>
                            @if($account->subAccountGroups->isNotEmpty())
                            <ul class="list-group list-group-flush ml-3">
                                @foreach($account->subAccountGroups as $subKey => $subAccount)
                                <li class="list-group-item">
                                    {{ $key . "." . ++$subKey . " " .$subAccount->name }}
                                    @if($subAccount->ledgers->isNotEmpty())
                                    <ul class="list-group ml-3 list-group-flush">
                                        @foreach($subAccount->ledgers as $ledgerKey => $ledger)
                                        <li class="list-group-item">
                                            {{ $key . "." . $subKey . "." .++$ledgerKey. " ". $ledger->name }}
                                            @if($ledger->subLedgers->isNotEmpty())
                                            <ul class="list-group ml-3 list-group-flush">
                                                @foreach($ledger->subLedgers as $subLedgerKey => $subLedger)
                                                <li class="list-group-item">{{$key . "." . $subKey . "." .$ledgerKey. ".". ++$subLedgerKey. " ".  $subLedger->name }}
                                                </li>
                                                @endforeach
                                            </ul>
                                            @endif
                                        </li>
                                        @endforeach
                                    </ul>
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </li>
                        @endforeach
                    </ul>
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

});
</script>
@endpush