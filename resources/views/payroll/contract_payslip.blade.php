<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Payslips</title>
    <style>
    @page {
        size: A5 portrait;
        margin: 15mm;
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
        font-size: 11px;
        margin-bottom: 6px;
    }

    .sub-header {
        text-align: center;
        font-size: 11px;
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
        font-size: 11px;
    }

    .text-right {
        text-align: right;
    }

    .section-title {
        font-weight: bold;
        margin: 6px 0 2px;
        border-bottom: 1px solid #000;
        font-size: 11px;
    }

    .footer {
        margin-top: 20px;
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
        </table>

        <!-- Earnings -->
        <div class="section-title">Monthly Salary</div>
        <table>
            <tr>
                <td colspan="2">Day Salary</td>
                <td class="text-right">{{ number_format($payroll->day_salary, 2) }}</td>
            </tr>
            <tr>
                <td >Worked Days</td>
                <td> @if($payroll->worked_days > 0)
                    {{ fmod($payroll->worked_days, 1) == 0 
                    ? number_format($payroll->worked_days, 0) 
                    : rtrim(rtrim(number_format($payroll->worked_days, 2), '0'), '.') }}
                    {{ $payroll->worked_days == 1 ? 'day' : 'days' }}
                    @else
                    
                    @endif</td>
                <td class="text-right">{{ number_format($payroll->worked_days, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2">Total Salary </td>
                <td class="text-right">{{ number_format($payroll->worked_days * $payroll->day_salary, 2) }}</td>
            </tr>
            @foreach ($earningComponents as $component)
            @php
            $value = $payroll->earnings->firstWhere('component_name', $component->name)->amount ?? 0;
            @endphp
            <tr>
                <td colspan="2">{{ $component->name }}</td>
                <td class="text-right">{{ number_format($value, 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2">Overtime</td>
                <td class="text-right">{{ number_format($payroll->overtime_amount ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Mercantile Holiday</td>
                <td>
                    @if($payroll->mercantile_days > 0)
                    {{ fmod($payroll->mercantile_days, 1) == 0 
                    ? number_format($payroll->mercantile_days, 0) 
                    : rtrim(rtrim(number_format($payroll->mercantile_days, 2), '0'), '.') }}
                    {{ $payroll->mercantile_days == 1 ? 'day' : 'days' }}
                    @else
                    -
                    @endif
                </td>
                <td class="text-right">{{ number_format($payroll->mercantile_days_amount, 2) }}</td>
            </tr>
            <tr>
                <td>Double Shift Payment</td>
                <td>
                    @if($payroll->extra_full_days > 0)
                    {{ fmod($payroll->extra_full_days, 1) == 0 
                    ? number_format($payroll->extra_full_days, 0) 
                    : rtrim(rtrim(number_format($payroll->extra_full_days, 2), '0'), '.') }}
                    {{ $payroll->extra_full_days == 1 ? 'day' : 'days' }}
                    @else
                    -
                    @endif
                </td>
                <td class="text-right">{{ number_format($payroll->extra_full_days_amount, 2) }}</td>
            </tr>
            <tr>
                <td>12 Hours Duty Payment</td>
                <td>
                    @if($payroll->extra_half_days > 0)
                    {{ fmod($payroll->extra_half_days, 1) == 0 
                    ? number_format($payroll->extra_half_days, 0) 
                    : rtrim(rtrim(number_format($payroll->extra_half_days, 2), '0'), '.') }}
                    @else
                    -
                    @endif
                </td>
                <td class="text-right">{{ number_format($payroll->extra_half_days_amount, 2) }}</td>
            </tr>
            <tr>
                <td>Poovarasan kuda Allowance</td>
                <td>
                    @if($payroll->poovarasan_kuda_allowance_150 > 0)
                    {{ fmod($payroll->poovarasan_kuda_allowance_150, 1) == 0 
                    ? number_format($payroll->poovarasan_kuda_allowance_150, 0) 
                    : rtrim(rtrim(number_format($payroll->poovarasan_kuda_allowance_150, 2), '0'), '.') }}
                    {{ $payroll->poovarasan_kuda_allowance_150 == 1 ? 'day' : 'days' }}
                    @else
                    -
                    @endif
                </td>
                <td class="text-right">{{ number_format($payroll->poovarasan_kuda_allowance_150_amount, 2) }}</td>
            </tr>
            <tr>
                <td>Extra Hours Duty</td>
                <td>
                    @if($payroll->labour_hours > 0)
                    {{ fmod($payroll->labour_hours, 1) == 0 
                    ? number_format($payroll->labour_hours, 0) 
                    : rtrim(rtrim(number_format($payroll->labour_hours, 2), '0'), '.') }}
                    {{ $payroll->labour_hours == 1 ? 'hour' : 'hours' }}
                    @else
                    -
                    @endif
                </td>
                <td class="text-right">{{ number_format($payroll->labour_amount, 2) }}</td>
            </tr>

            <tr>
                <td colspan="2"><b>Total</b></td>
                <td class="text-right">
                    <b>{{ number_format($payroll->gross_earnings + $payroll->mercantile_days_amount + $payroll->extra_full_days_amount + $payroll->extra_half_days_amount + $payroll->poovarasan_kuda_allowance_150_amount + $payroll->labour_amount, 2) }}</b>
                </td>
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
                <td><b>Total</b></td>
                <td class="text-right"><b>{{ number_format($payroll->total_deductions, 2) }}</b></td>
            </tr>
        </table>
        <!-- Net Pay -->
        <table>
            <tr style="border-top: 1px solid #000; border-bottom: double 3px #000;">
                <td><b>Total</b></td>
                <td class="text-right"><b>{{ number_format($payroll->net_pay, 2) }}</b></td>
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