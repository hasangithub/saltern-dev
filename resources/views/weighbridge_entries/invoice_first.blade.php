@extends('layout.print_a6')
@section('section-title', 'WEIGHMENT')
@section('content')


<div class="details">
    <table>
        <tr>
            <td><strong>Rec No:</strong></td>
            <td>{{ $entry->id }}</td>
        </tr>
        <tr>
            <td><strong>Owner:</strong></td>
            <td>{{ $entry->membership->owner->name_with_initial ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Yahai:</strong></td>
            <td>{{ $entry->membership->saltern->yahai->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Waikal:</strong></td>
            <td>{{ $entry->membership->saltern->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Buyer:</strong></td>
            <td>{{ $entry->buyer->full_name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Vehicle No:</strong></td>
            <td>{{ $entry->vehicle_id }}</td>
        </tr>
        <tr>
            <td><strong>First Weight:</strong></td>
            <td>{{ number_format($entry->initial_weight, 2) }} kg</td>
        </tr>
    
        <tr>
            <td><strong>Date of Distribution:</strong></td>
            <td>{{ \Carbon\Carbon::parse($entry->transaction_date)->format('Y-m-d') }}</td>
        </tr>
    </table>
</div>

@endsection