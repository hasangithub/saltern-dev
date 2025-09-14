<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Ledger Report</title>
    <style>
    body {
        font-family: sans-serif;
        font-size: 10px;
        line-height: 1.5;
    }

    .container {
        width: 100%;
        margin: 0 auto;
    }

    h4,
    p {
        margin: 0;
        padding: 4px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 6px;
    }

    th {
        background-color: #f0f0f0;
    }

    .text-right {
        text-align: right;
    }

    .fw-bold {
        font-weight: bold;
    }

    .bg-light {
        background-color: #f9f9f9;
    }

    .table-secondary {
        background-color: #eee;
    }

    .mt-2 {
        margin-top: 10px;
    }

    .report-header {
        border-bottom: 2px solid #444;
        padding-bottom: 10px;
        margin-bottom: 20px;
        font-family: 'Arial', sans-serif;
    }

    .report-header .org-name {
        font-size: 16px;
        font-weight: bold;
        color: #1a1a1a;
        text-align: center;
    }

    .report-header .report-title {
        font-size: 14px;
        font-weight: bold;
        margin-top: 5px;
        color: #333;
        text-align: center;
    }

    .report-header hr {
        margin-top: 10px;
        border: 0;
        border-top: 1px dashed #999;
    }

    @yield('custom-css')
    </style>
</head>

<body>
    <div class="container">
        <div class="report-header">
            <div class="org-name">
                {{ $organization ?? 'PUTTALAM SALT PRODUCERS WELFARE SOCIETY LTD' }}
            </div>

            <div class="report-title">
                @yield('section-title')
            </div>

            <table style="width: 100%; font-size: 12px; margin-bottom: 10px; border:0px solid;">
                <tr>
                    @if($fromDate)
                    <td style="text-align: left;">
                        <strong>For:</strong>
                        {{ $fromDate }} to {{ $toDate }}
                    </td>
                    @endif
                    <td style="text-align: right;">
                        <strong>Printed on:</strong>
                        {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}
                    </td>
                </tr>
            </table>

        </div>

        @yield('content')
    </div>
</body>

</html>