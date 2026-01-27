<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Print')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
    @page {
        margin: 0cm;
        size: A6 portrait;
    }

    body {
        font-family: "Times New Roman", Times, serif;
        font-size: 11px;
        line-height: 1.3;
        margin: 0;
        padding: 10;
    }


    .center {
        text-align: center;
    }

    .header {
        text-align: center;
        margin-bottom: 12px;
    }

    .header-title {
        font-size: 12px;
        font-weight: bold;
        letter-spacing: 0.5px;
        line-height: 1.3;
        text-transform: uppercase;
    }

    .sub-header {
        font-size: 11.5px;
        color: #333;
        margin-top: 2px;
    }

    .section-divider {
        border-top: 1px dashed #000;
        margin: 6px 0;
    }

    .section-title {
        font-size: 12px;
        text-decoration: underline;
        margin-top: 4px;
    }

    .details {
        margin-bottom: 1px;
    }

    .details table {
        width: 100%;
        border-collapse: collapse;
    }

    .details td {
        padding: 1px 3px;
        vertical-align: top;
        font-size: 14px;
    }

    .footer {
        margin-top: 10px;
    }

    .signature {
        float: right;
        text-align: center;
        margin-top: 10px;
    }

    .line {
        border-top: 1px solid #000;
        margin-top: 20px;
        width: 100px;
    }

    .amount-right {
        text-align: right;
        font-weight: bold;
    }

    .content-wrapper {
        width: 100%;
        padding: 10px;
        border: 1px;
        box-sizing: border-box;
    }

    @yield('styles')
    </style>
</head>

<body @if(!isset($from_pdf)) onload="window.print()" @endif>
    <div class="content-wrapper">
        <div class="header">
            <div class="header-title">PUTTALAM SALT PRODUCERS WELFARE SOCIETY LTD</div>
            <div class="sub-header"> Saltern Yard, Mannar Road, Puttalam</div>
            <div class="sub-header"> Tel: 032-2265260 / Email: pspwsl@gmail.com </div>
            <div class="section-divider"></div>
            <div class="section-title"> @yield('section-title')</div>
        </div>
        @yield('content')
    </div>
</body>

</html>