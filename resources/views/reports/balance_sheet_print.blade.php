<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Balance Sheet</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        td, th {
            border: 1px solid #000;
            padding: 6px;
        }

        .text-right {
            text-align: right;
        }

        .col-6 {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }

        .divider {
            height: 20px;
        }

        strong {
            display: block;
            margin: 5px 0;
        }

        @page {
            size: A4;
            margin: 20mm;
        }
    </style>
</head>
<body>
    <h2>Balance Sheet</h2>

    <div class="col-6">
        <strong>Assets</strong>

        @foreach ($data['Assets'] as $group)
            <strong>{{ $group['name'] }}</strong>
            <table>
                @foreach ($group['subGroups'] as $subName => $sub)
                    <tr>
                        <td colspan="2"><strong>{{ $subName }}</strong></td>
                    </tr>
                    @foreach ($sub['ledgers'] as $ledger)
                        <tr>
                            <td>{{ $ledger['name'] }}</td>
                            <td class="text-right">{{ number_format($ledger['total'], 2) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td><strong>Total</strong></td>
                        <td class="text-right"><strong>{{ number_format($sub['total'], 2) }}</strong></td>
                    </tr>
                @endforeach
            </table>
        @endforeach

        <table>
            <tr>
                <td><strong>Total Assets</strong></td>
                <td class="text-right"><strong>{{ number_format($assetsTotal, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="col-6">
        <strong>Liabilities and Equity</strong>

        <strong>Equity (Capital)</strong>
        <table>
            @foreach ($data['Equity'] as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td class="text-right">{{ number_format($item['total'], 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td><strong>Total Equity</strong></td>
                <td class="text-right"><strong>{{ number_format($equityTotal, 2) }}</strong></td>
            </tr>
        </table>

        <strong>Current Liabilities</strong>
        <table>
            @foreach ($data['CurrentLiabilities'] as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td class="text-right">{{ number_format($item['total'], 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td><strong>Total Liabilities</strong></td>
                <td class="text-right"><strong>{{ number_format($liabilitiesTotal, 2) }}</strong></td>
            </tr>
        </table>

        <table>
            <tr>
                <td><strong>Total Equity & Liabilities</strong></td>
                <td class="text-right"><strong>{{ number_format($equityTotal + $liabilitiesTotal, 2) }}</strong></td>
            </tr>
        </table>
    </div>
</body>
</html>
