
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <title>Test Cheque</title>

    <style>
        @page {
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 177mm;
            height: 88mm;
            font-family: DejaVu Sans, sans-serif;
            color: #000;
        }

        .cheque {
            position: relative;
            width: 177mm;
            height: 88mm;
            overflow: hidden;
            background: #fff;
        }

        /* CHEQUE DATE */
        .cheque-date {
            position: absolute;
            top: 8mm;
            right: 10mm;
            width: 45mm;
            text-align: center;
            font-size: 11pt;
            letter-spacing: 3px;
        }

        /* PAYEE NAME */
        .cheque-payee {
            position: absolute;
            top: 25mm;
            left: 12mm;
            width: 150mm;
            font-size: 12pt;
            font-weight: bold;
        }

        /* AMOUNT IN WORDS */
        .cheque-words {
            position: absolute;
            top: 39mm;
            left: 12mm;
            width: 105mm;
            font-size: 10pt;
            line-height: 1.5;
            font-weight: bold;
        }

        /* NUMERIC AMOUNT */
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

        /* CHEQUE NUMBER - optional reference */
        .cheque-number {
            position: absolute;
            top: 3mm;
            left: 12mm;
            font-size: 8pt;
        }
    </style>
</head>

<body>

<div class="cheque">

    @if($cheque->cheque_no)
        
    @endif

    <div class="cheque-date">
        {{ \Carbon\Carbon::parse($cheque->cheque_date)->format('dmY') }}
    </div>

    <div class="cheque-payee">
        {{ $cheque->name }}
    </div>

    <div class="cheque-words">
        **{{ $amountWords }}
    </div>

    <div class="cheque-amount">
        **{{ number_format((float) $cheque->amount, 2) }}**
    </div>

</div>

</body>
</html>
