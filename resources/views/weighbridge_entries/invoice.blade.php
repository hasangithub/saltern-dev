<!DOCTYPE html>
<html>
<head>
    <title>Weighbridge Invoice</title>
    <style>
        /* Your print styling */
    </style>
</head>
<body onload="window.print()">
    <h2>Weighbridge Invoice</h2>

    <p><strong>Date:</strong> {{ $entry->transaction_date }}</p>
    <p><strong>Vehicle ID:</strong> {{ $entry->vehicle_id }}</p>
    <p><strong>Initial Weight:</strong> {{ $entry->initial_weight }} kg</p>
    <p><strong>Tare Weight:</strong> {{ $entry->tare_weight }} kg</p>
    <p><strong>Net Weight:</strong> {{ $entry->net_weight }} kg</p>
    <p><strong>Buyer:</strong> {{ $entry->buyer->name ?? '-' }}</p>
    <p><strong>Owner:</strong> {{ $entry->membership->owner->name_with_initial ?? '-' }}</p>

    <!-- Optional: Auto-close window -->
    <script>
        window.onafterprint = function () {
            // Redirect after printing
            window.location.href = "{{ route('weighbridge_entries.create') }}";
        };

        // Fallback in case user cancels print or browser doesn't trigger onafterprint reliably
        setTimeout(function() {
            window.location.href = "{{ route('weighbridge_entries.create') }}";
        }, 5000); // 5 seconds
    </script>
</body>
</html>
