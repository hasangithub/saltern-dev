
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Refund Cheque</title>

    <style>
        @page {
            size: 88mm 177mm;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 88mm;
            height: 177mm;
            font-family: DejaVu Sans, sans-serif;
            color: #000;
        }

        /*
        |--------------------------------------------------------------------------
        | PORTRAIT PAGE
        |--------------------------------------------------------------------------
        */

        .cheque-page {
            position: relative;
            width: 88mm;
            height: 177mm;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        /*
        |--------------------------------------------------------------------------
        | LANDSCAPE CHEQUE LAYOUT
        |--------------------------------------------------------------------------
        | Original cheque dimensions:
        | Width  = 177mm
        | Height = 88mm
        |
        | Rotate clockwise 90 degrees for portrait printing.
        */

        .cheque-layout {
            position: absolute;
            top: 0;
            left: 88mm;

            width: 177mm;
            height: 88mm;

            transform-origin: top left;
            transform: rotate(90deg) scale(1);

            overflow: hidden;
            background: #fff;
        }

        /*
        |--------------------------------------------------------------------------
        | CHEQUE FIELD POSITIONS
        |--------------------------------------------------------------------------
        | These are initial estimated positions.
        | Adjust them according to your actual Amana cheque.
        */

        /* Cheque number */
        .cheque-number {
            position: absolute;
            top: 4mm;
            left: 12mm;
            width: 40mm;
            font-size: 9pt;
        }

        /* Date */
        .cheque-date {
            position: absolute;
            top: 8mm;
            right: 10mm;
            width: 45mm;
            text-align: center;
            font-size: 11pt;
            letter-spacing: 3px;
            font-weight: bold;
        }

        /* Payee name */
        .cheque-payee {
            position: absolute;
            top: 25mm;
            left: 12mm;
            width: 150mm;
            font-size: 12pt;
            font-weight: bold;
            white-space: nowrap;
            overflow: hidden;
        }

        /* Amount in words */
        .cheque-words {
            position: absolute;
            top: 39mm;
            left: 12mm;
            width: 105mm;
            font-size: 10pt;
            line-height: 1.5;
            font-weight: bold;
        }

        /* Numeric amount */
        .cheque-amount {
            position: absolute;
            top: 39mm;
            right: 10mm;
            width: 43mm;
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            white-space: nowrap;
        }

        /*
        |--------------------------------------------------------------------------
        | PDF PRINT
        |--------------------------------------------------------------------------
        */

        @media print {
            html, body {
                width: 88mm;
                height: 177mm;
                margin: 0;
                padding: 0;
            }

            .cheque-page {
                margin: 0;
                padding: 0;
                page-break-after: avoid;
            }
        }
    </style>
</head>

<body>

<div class="cheque-page">

    <div class="cheque-layout">

        {{-- CHEQUE NUMBER --}}
        @if(!empty($cheque->cheque_no))
            <div class="cheque-number">
                {{ $cheque->cheque_no }}
            </div>
        @endif

        {{-- DATE --}}
        <div class="cheque-date">
            {{ \Carbon\Carbon::parse($cheque->cheque_date)->format('dmY') }}
        </div>

        {{-- PAYEE NAME --}}
        <div class="cheque-payee">
            {{ $cheque->name }}
        </div>

        {{-- AMOUNT IN WORDS --}}
        <div class="cheque-words">
            **{{ $amountWords }}
        </div>

        {{-- NUMERIC AMOUNT --}}
        <div class="cheque-amount">
            **{{ number_format((float) $cheque->amount, 2) }}**
        </div>

    </div>

</div>

</body>
</html>
