<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 0;
        }
        .payslip {
            page-break-after: always;
        }
        .header {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }
        .sub-header {
            text-align: center;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        td, th {
            padding: 3px 5px;
            font-size: 12px;
        }
        .text-right {
            text-align: right;
        }
        .section-title {
            font-weight: bold;
            margin-top: 10px;
            border-bottom: 1px solid #000;
        }
        .footer {
            margin-top: 30px;
            width: 100%;
        }
        .footer td {
            width: 50%;
            text-align: center;
            padding-top: 40px;
        }
    </style>
</head>
<body>
    @foreach($batch->payrolls as $payroll)
    <div class="payslip">
        <div class="header">Puttalam Salt Producers Welfare Society Ltd</div>
        <div class="sub-header">Mannar Road, Puttalam</div>
        <div class="sub-header"><strong>Pay Sheet - {{ date('F Y', strtotime($batch->pay_period)) }}</strong></div>

        <table>
            <tr><td><b>Name:</b></td><td>{{ $payroll->employee->user->name }}</td></tr>
            <tr><td><b>Post:</b></td><td>{{ $payroll->employee->designation ?? '-' }}</td></tr>
            <tr><td><b>E.P.F No:</b></td><td>{{ $payroll->employee->epf_no ?? '-' }}</td></tr>
        </table>

        <div class="section-title">Monthly Salary</div>
        <table>
            <tr><td>Basic Salary</td><td class="text-right">{{ number_format($payroll->basic_salary, 2) }}</td></tr>
            <tr><td>Cost of Living Allowance</td><td class="text-right">{{ number_format($payroll->cost_of_living_allowance ?? 0, 2) }}</td></tr>
            <tr><td>Fixed Allowance</td><td class="text-right">{{ number_format($payroll->fixed_allowance ?? 0, 2) }}</td></tr>
            <tr><td>Overtime</td><td class="text-right">{{ number_format($payroll->overtime_amount ?? 0, 2) }}</td></tr>
            <tr><td><b>Total</b></td><td class="text-right"><b>{{ number_format($payroll->gross_earnings, 2) }}</b></td></tr>
        </table>

        <div class="section-title">Deductions</div>
        <table>
            <tr><td>Salary Advance</td><td class="text-right">{{ number_format($payroll->salary_advance ?? 0, 2) }}</td></tr>
            <tr><td>Festival Advance</td><td class="text-right">{{ number_format($payroll->festival_advance ?? 0, 2) }}</td></tr>
            <tr><td>Loan</td><td class="text-right">{{ number_format($payroll->loan ?? 0, 2) }}</td></tr>
            <tr><td>Union</td><td class="text-right">{{ number_format($payroll->union ?? 0, 2) }}</td></tr>
            <tr><td>E.P.F 8%</td><td class="text-right">{{ number_format($payroll->epf ?? 0, 2) }}</td></tr>
            <tr><td><b>Total</b></td><td class="text-right"><b>{{ number_format($payroll->total_deductions, 2) }}</b></td></tr>
        </table>

        <p><b>Balance:</b> {{ number_format($payroll->net_pay, 2) }}</p>

        <div class="section-title">Employer Contribution</div>
        <table>
            <tr><td>EPF 12%</td><td class="text-right">{{ number_format($payroll->epf12 ?? 0, 2) }}</td></tr>
            <tr><td>ETF 3%</td><td class="text-right">{{ number_format($payroll->etf3 ?? 0, 2) }}</td></tr>
        </table>

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
