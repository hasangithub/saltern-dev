<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Payslips</title>
    <style>
    @page {
        size: A5 portrait;
        margin: 5mm;
    }

    body {
        font-family: sans-serif;
        font-size: 11px;
        margin: 0;
    }

    .payslip {
        width: 100%;
        border: 1px solid #000;
        padding: 5px;
        box-sizing: border-box;
        page-break-after: always;
        /* New page after each slip */
        page-break-inside: avoid;
        /* Avoid splitting slip across pages */
    }

    .header {
        text-align: center;
        font-weight: bold;
        font-size: 10px;
        margin-bottom: 6px;
    }

    .sub-header {
        text-align: center;
        font-size: 10px;
        margin-bottom: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 6px;
    }

    td,
    th {
        padding: 2px 4px;
        font-size: 10px;
    }

    .text-right {
        text-align: right;
    }

    .section-title {
        font-weight: bold;
        margin: 6px 0 2px;
        border-bottom: 1px solid #000;
        font-size: 10px;
    }

    .footer {
        margin-top: 10px;
        width: 100%;
    }

    .footer td {
        width: 50%;
        text-align: center;
        padding-top: 10px;
        font-size: 11px;
    }
    </style>
</head>

<body>

    @foreach($batch->payrolls as $payroll)
    <div class="payslip">
        <!-- Header -->
        <div class="header">Puttalam Salt Producers Welfare Society Ltd</div>
        <div class="sub-header">
            <strong>Payslip - {{ date('F Y', strtotime($batch->pay_period)) }}</strong>
        </div>

        <!-- Employee Info -->
        <table>
            <tr>
                <td><b>Employee Name:</b></td>
                <td>{{ $payroll->employee->user->name }}</td>
            </tr>
            <tr>
                <td><b>Post:</b></td>
                <td>{{ $payroll->employee->designation ?? '-' }}</td>
            </tr>
            <tr>
                <td><b>E.P.F No:</b></td>
                <td>{{ $payroll->employee->epf_number ?? '-' }}</td>
            </tr>
        </table>

        <!-- Earnings -->
        <div class="section-title">Monthly Salary</div>
        <table>
            <tr>
                <td>Basic Salary</td>
                <td class="text-right">{{ number_format($payroll->basic_salary, 2) }}</td>
            </tr>
            <tr>
                <td>No Pay</td>
                <td class="text-right">{{ number_format($payroll->no_pay, 2) }}</td>
            </tr>
            <tr>
                <td>Basic Salary (after deduction)</td>
                <td class="text-right">{{ number_format($payroll->basic_salary - $payroll->no_pay, 2) }}</td>
            </tr>
            @foreach ($earningComponents as $component)
            @php
            $value = $payroll->earnings->firstWhere('component_name', $component->name)->amount ?? 0;
            @endphp
            <tr>
                <td>{{ $component->name }}</td>
                <td class="text-right">{{ number_format($value, 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <td>Overtime</td>
                <td class="text-right">{{ number_format($payroll->overtime_amount ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td><b>Total</b></td>
                <td class="text-right"><b>{{ number_format($payroll->gross_earnings, 2) }}</b></td>
            </tr>
        </table>

        <!-- Deductions -->
        <div class="section-title">Deductions</div>
        <table>
            @foreach ($deductionComponents as $component)
            @php
            $value = $payroll->deductions->firstWhere('component_name', $component->name)->amount ?? 0;
            @endphp
            <tr>
                <td>{{ $component->name }}</td>
                <td class="text-right">{{ number_format($value, 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <td>E.P.F 8%</td>
                <td class="text-right">{{ number_format($payroll->epf_employee ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td><b>Total</b></td>
                <td class="text-right"><b>{{ number_format($payroll->total_deductions, 2) }}</b></td>
            </tr>
        </table>
        <div class="section-title">Extras</div>
        <table>
            <tr>
                <td><b>Mercantile @if($payroll->mercantile_days > 0)
                        ({{ fmod($payroll->mercantile_days, 1) == 0 
                ? number_format($payroll->mercantile_days, 0) 
                : rtrim(rtrim(number_format($payroll->mercantile_days, 2), '0'), '.') }})
                        @endif</b></td>
                <td class="text-right"><b>{{ number_format($payroll->mercantile_days_amount, 2) }}</b></td>
            </tr>
            <tr>
                <td><b>Full Days @if($payroll->extra_full_days > 0)
                        ({{ fmod($payroll->extra_full_days, 1) == 0 
                ? number_format($payroll->extra_full_days, 0) 
                : rtrim(rtrim(number_format($payroll->extra_full_days, 2), '0'), '.') }})
                        @endif</b></td>
                <td class="text-right"><b>{{ number_format($payroll->extra_full_days_amount, 2) }}</b></td>
            </tr>
            <tr>
                <td><b>Half Days @if($payroll->extra_half_days > 0)
                        ({{ fmod($payroll->extra_half_days, 1) == 0 
                ? number_format($payroll->extra_half_days, 0) 
                : rtrim(rtrim(number_format($payroll->extra_half_days, 2), '0'), '.') }})
                        @endif</b></td>
                <td class="text-right"><b>{{ number_format($payroll->extra_half_days_amount, 2) }}</b></td>
            </tr>
            <tr>
                <td><b>Poovarasan kuda allow @if($payroll->poovarasan_kuda_allowance_150 > 0)
                        ({{ fmod($payroll->poovarasan_kuda_allowance_150, 1) == 0 
                ? number_format($payroll->poovarasan_kuda_allowance_150, 0) 
                : rtrim(rtrim(number_format($payroll->poovarasan_kuda_allowance_150, 2), '0'), '.') }})
                        @endif</b></td>
                <td class="text-right"><b>{{ number_format($payroll->poovarasan_kuda_allowance_150_amount, 2) }}</b>
                </td>
            </tr>
            <tr>
                <td><b>Extra Hours @if($payroll->labour_hours > 0)
                        ({{ fmod($payroll->labour_hours, 1) == 0 
                ? number_format($payroll->labour_hours, 0) 
                : rtrim(rtrim(number_format($payroll->labour_hours, 2), '0'), '.') }})
                        @endif</b></td>
                <td class="text-right"><b>{{ number_format($payroll->labour_amount, 2) }}</b>
                </td>
            </tr>
        </table>
        <!-- Net Pay -->
        <table>
            <tr>
                <td><b>Balance</b></td>
                <td class="text-right"><b>{{ number_format($payroll->net_pay, 2) }}</b></td>
            </tr>
        </table>

        <!-- Employer Contribution -->
        <div class="section-title">Employer Contribution</div>
        <table>
            <tr>
                <td>EPF 12%</td>
                <td class="text-right">{{ number_format($payroll->epf_employer ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>ETF 3%</td>
                <td class="text-right">{{ number_format($payroll->etf ?? 0, 2) }}</td>
            </tr>
        </table>

        <!-- Footer -->
        <table class="footer">
            <tr>
                <td>Authorized</td>
                <td>Signature</td>
            </tr>
        </table>
    </div>
    @endforeach

</body>

</html>