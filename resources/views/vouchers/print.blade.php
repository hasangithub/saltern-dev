<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Payment Voucher</title>
    <style>
    body {
        font-family: "Times New Roman", sans-serif;
        font-size: 12.5px;
        margin: 0;
        padding: 1px;
    }

    .page {
        width: 100%;
        height: 14 cm;
        /* Half of A4 in portrait */
        box-sizing: border-box;
    }

    .header {
        text-align: center;
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .subheader {
        text-align: center;
        font-size: 12px;
        text-decoration: underline;
        margin-bottom: 10px;
    }

    table.details {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }

    table.details td {
        padding: 4px;
        vertical-align: top;
    }

    .label {
        font-weight: bold;
        width: 80px;
    }

    .signatures {
        width: 100%;
        margin-top: 20px;
    }

    .signatures td {
        width: 16.66%;
        text-align: center;
        padding-top: 30px;
    }

    .received {
        text-align: right;
        margin-top: 20px;
    }

    .stamp-box {
        border: 1px solid #000;
        width: 120px;
        height: 50px;
        float: right;
        text-align: center;
        line-height: 50px;
        font-size: 10px;
        margin-top: 10px;
    }
    </style>
</head>

<body>
    <div class="page">
        <div style="text-align: center; font-family: Arial, sans-serif; margin-bottom: 10px;">
            <h2 style="margin: 0; font-size: 18px; font-weight: bold;">
                PUTTALAM SALT PRODUCERS WELFARE SOCIETY LTD
            </h2>
            <p style="margin: 2px 0; font-size: 14px;">
                Saltern Yard, Mannar Road, Puttalam
            </p>
            <p style="margin: 2px 0; font-size: 14px;">
                Tel: 032-2265260 / Email: pspwsl@gmail.com
            </p>
            <hr>
            <h3 style="margin-top: 15px; text-decoration: underline; font-size: 16px;">
                Payment Voucher @if($voucher->payment_method_id === 1) - CHEQUE @else - CASH @endif
            </h3>
        </div>

        <table class="details">
            <tr>
                <td class="label">Voucher ID:</td>
                <td>{{ $voucher->id }}</td>
                <td class="label">Ledger:</td>
                <td>{{ $voucher->ledger->name ?? '-' }}</td>
                <td class="label">Date:</td>
                <td>{{ \Carbon\Carbon::parse($voucher->date)->format('d-m-Y') }}</td>
            </tr>
        </table>

        <table class="details">
            <tr>
                <td class="label">Name:</td>
                <td colspan="5">{{ $voucher->name }}</td>
            </tr>
            <tr>
                <td class="label">Address:</td>
                <td colspan="5">{{ $voucher->address }}</td>
            </tr>
            <tr>
                <td class="label">Description:</td>
                <td colspan="5" style="padding-bottom:20px">{{ $voucher->description }}</td>
            </tr>
            <tr>
                <td class="label">Amount:</td>
                <td>Rs. {{ number_format($voucher->amount, 2) }}</td>
            </tr>
        </table>

        <table width="100%" cellpadding="6" cellspacing="0"
            style="margin-top: 10px; border-collapse: collapse; font-size: 14px; border: 1px solid #333;">
            @if($voucher->payment_method_id === 1)
            <tr>
                <td style="border: 1px solid #333; font-weight: bold; width: 15%;">Cheque No</td>
                <td style="border: 1px solid #333; width: 18%;">{{ $voucher->cheque_no }}</td>

                <td style="border: 1px solid #333; font-weight: bold; width: 15%;">Bank</td>
                <td style="border: 1px solid #333; width: 18%;">{{ $voucher->bank->name }}</td>

                <td style="border: 1px solid #333; font-weight: bold; width: 15%;">Cheque Date</td>
                <td style="border: 1px solid #333; width: 19%;">
                    {{ $voucher->cheque_date ? \Carbon\Carbon::parse($voucher->cheque_date)->format('d-m-Y') : '-' }}
                </td>
            </tr>
            @endif
           
        </table>
       
        Passed for payment
        <table style="width: 100%; margin-top: 30px; font-size: 12px; border-collapse: collapse;">
            <tr>
                <!-- Left side -->
                <td style="width: 50%; vertical-align: top;">
                    <table style="width: 100%; border: 0px solid #000;">
                        <tr>
                            <td style="border: 0px solid #000; padding: 5px;">Prepared By
                                ...............................................................</td>
                        </tr>
                        <tr>
                            <td style="border: 0px solid #000; padding: 5px;">Checked By
                                ...............................................................</td>
                        </tr>
                        <tr>
                            <td style="border: 0px solid #000; padding: 5px;">Certified By
                                ...............................................................</td>
                        </tr>
                    </table>
                </td>

                <!-- Right side -->
                <td style="width: 50%; vertical-align: top;">
                    <table style="width: 100%; border: 0px solid #000;">
                        <tr>
                            <td style="border: 0px solid #000; padding: 5px;">President
                                ...............................................................</td>
                        </tr>
                        <tr>
                            <td style="border: 0px solid #000; padding: 5px;">Secretary
                                ...............................................................</td>
                        </tr>
                        <tr>
                            <td style="border: 0px solid #000; padding: 5px;">Treasurer
                                ...............................................................</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>



        <div class="stamp-box">
            Received
        </div>
    </div>
</body>

</html>