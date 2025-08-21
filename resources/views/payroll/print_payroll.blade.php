<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Other Income Invoice</title>
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        color: #333;
        margin: 40px;
    }

    .header {
        text-align: center;
        margin-bottom: 20px;
    }

    .header-title {
        font-size: 20px;
        font-weight: bold;
        color: #000;
    }

    .sub-header {
        font-size: 13px;
        color: #555;
    }

    .section-divider {
        margin: 15px 0;
        border-top: 2px solid #444;
    }

    .section-title {
        font-size: 16px;
        font-weight: bold;
        color: #222;
        text-transform: uppercase;
        text-align: center;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 30px;
    }

    th,
    td {
        border: 1px solid #999;
        padding: 10px;
        font-size: 13px;
    }

    th {
        background-color: #f0f0f0;
        color: #000;
        text-align: left;
    }

    .details {
        margin-top: 20px;
        font-size: 14px;
    }

    .details td {
        padding: 4px 8px;
    }

    .footer {
        margin-top: 16px;
    }

    .signature {
        float: right;
        text-align: center;
        margin-top: 15px;
    }

    .line {
        border-top: 1px solid #000;
        margin-top: 20px;
        width: 100px;
    }


    .right-align {
        text-align: right;
    }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-title">PUTTALAM SALT PRODUCERS WELFARE SOCIETY LTD</div>
        <div class="sub-header">Saltern Yard, Mannar Road, Puttalam</div>
        <div class="sub-header">Tel: 032-2265260 / Email: pspwsl@gmail.com</div>
        <div class="section-divider"></div>
        <div class="section-title"></div>
    </div>

    @php
    // Precompute totals
    $totalBasic = 0;
    $totalOvertimeHours = 0;
    $totalOvertimeAmount = 0;
    $totalGross = 0;
    $totalDeductions = 0;
    $totalNet = 0;

    $earningTotals = [];
    $deductionTotals = [];
    $epfTotal = 0;
    $etfTotal = 0;

    foreach($batch->payrolls as $payroll) {
    $emp = $payroll->employee;
    $basic = $payroll->basic_salary;
    $overtimeHours = $payroll->overtime_hours ?? 0;
    $overtimeAmount = $payroll->overtime_amount ?? 0;

    $totalBasic += $basic;
    $totalOvertimeHours += $overtimeHours;
    $totalOvertimeAmount += $overtimeAmount;

    $gross = $basic + $overtimeAmount;

    foreach($earningComponents as $ec) {
    $earning = $payroll->earnings->firstWhere('component_id', $ec->id);
    $amt = $earning->amount ?? $ec->default_amount ?? 0;
    $gross += $amt;
    $earningTotals[$ec->id] = ($earningTotals[$ec->id] ?? 0) + $amt;
    }

    $dedTotal = 0;
    foreach($deductionComponents as $dc) {
    $deduction = $payroll->deductions->firstWhere('component_id', $dc->id);
    $amount = $deduction->amount ?? 0;
    $name = strtolower($dc->name);

    if(str_contains($name,'epf') && str_contains($name,'employer')) {
    $epfTotal += $basic * 0.12;
    $amount = $basic * 0.12;
    }
    if(str_contains($name,'etf')) {
    $etfTotal += $basic * 0.03;
    $amount = $basic * 0.03;
    }

    $dedTotal += $amount;
    $deductionTotals[$dc->id] = ($deductionTotals[$dc->id] ?? 0) + $amount;
    }

    $totalGross += $gross;
    $totalDeductions += $dedTotal;
    $totalNet += $gross - $dedTotal;
    }
    @endphp

    <div style="width:100%; font-size:12px; font-family:Arial, sans-serif;">
        <h4 style="text-align:center;">Payroll — {{ $batch->pay_period }}</h4>
        <p style="text-align:center;">Status: {{ ucfirst($batch->status) }}</p>

        <table width="100%" border="1" cellspacing="0" cellpadding="4"
            style="border-collapse: collapse; margin-bottom:20px;">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Basic</th>
                    @foreach($earningComponents as $ec)
                    <th>{{ $ec->name }}</th>
                    @endforeach
                    <th>Hours</th>
                    <th>Overtime</th>
                    <th>Gross</th>
                    @foreach($deductionComponents as $dc)
                    <th>{{ $dc->name }}</th>
                    @endforeach
                    <th>Deductions</th>
                    <th>Net</th>
                </tr>
            </thead>
            <tbody>
                @foreach($batch->payrolls as $payroll)
                @php
                $emp = $payroll->employee;
                $gross = $payroll->gross_earnings;
                @endphp
                <tr>
                    <td>{{ $emp->user->name }}</td>
                    <td>{{ number_format($payroll->basic_salary,2) }}</td>
                    @foreach($earningComponents as $ec)
                    @php
                    $earning = $payroll->earnings->firstWhere('component_id', $ec->id);
                    $amt = $earning->amount ?? $ec->default_amount ?? 0;
                    @endphp
                    <td>{{ number_format($amt,2) }}</td>
                    @endforeach
                    <td>{{ $payroll->overtime_hours ?? 0 }}</td>
                    <td>{{ number_format($payroll->overtime_amount ?? 0,2) }}</td>
                    <td>{{ number_format($gross,2) }}</td>
                    @foreach($deductionComponents as $dc)
                    @php
                    $ded = $payroll->deductions->firstWhere('component_id', $dc->id);
                    $dedAmt = $ded->amount ?? 0;
                    if(str_contains(strtolower($dc->name),'epf') && str_contains(strtolower($dc->name),'employer')) {
                    $dedAmt = $payroll->basic_salary * 0.12;
                    }
                    if(str_contains(strtolower($dc->name),'etf')) {
                    $dedAmt = $payroll->basic_salary * 0.03;
                    }
                    @endphp
                    <td>{{ number_format($dedAmt,2) }}</td>
                    @endforeach
                    <td>{{ number_format($payroll->total_deductions,2) }}</td>
                    <td>{{ number_format($payroll->net_pay,2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th>{{ number_format($totalBasic,2) }}</th>
                    @foreach($earningComponents as $ec)
                    <th>{{ number_format($earningTotals[$ec->id] ?? 0,2) }}</th>
                    @endforeach
                    <th>{{ number_format($totalOvertimeHours,2) }}</th>
                    <th>{{ number_format($totalOvertimeAmount,2) }}</th>
                    <th>{{ number_format($totalGross,2) }}</th>
                    @foreach($deductionComponents as $dc)
                    <th>{{ number_format($deductionTotals[$dc->id] ?? 0,2) }}</th>
                    @endforeach
                    <th>{{ number_format($totalDeductions,2) }}</th>
                    <th>{{ number_format($totalNet,2) }}</th>
                </tr>
            </tfoot>
        </table>

        <!-- Summary -->
        <div style="width:100%; display:flex; justify-content:space-between; margin-top:20px;">
            <div style="width:48%;">
                <table width="100%" border="1" cellspacing="0" cellpadding="4" style="border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th colspan="2">Earnings Summary</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Basic Salary</td>
                            <td>{{ number_format($totalBasic,2) }}</td>
                        </tr>
                        @foreach($earningComponents as $ec)
                        <tr>
                            <td>{{ $ec->name }}</td>
                            <td>{{ number_format($earningTotals[$ec->id] ?? 0,2) }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td>Overtime Amount</td>
                            <td>{{ number_format($totalOvertimeAmount,2) }}</td>
                        </tr>
                        <tr>
                            <td>EPF 12% (Employer)</td>
                            <td>{{ number_format($epfTotal,2) }}</td>
                        </tr>
                        <tr>
                            <td>ETF</td>
                            <td>{{ number_format($etfTotal,2) }}</td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td>Total Earnings</td>
                            <td>{{ number_format($totalBasic + array_sum($earningTotals) + $totalOvertimeAmount + $epfTotal + $etfTotal,2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="width:48%;">
                <table width="100%" border="1" cellspacing="0" cellpadding="4" style="border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th colspan="2">Deductions Summary</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deductionComponents as $dc)
                        @php
                        $name = strtolower($dc->name);
                        @endphp

                        @if(!str_contains($name, 'epf') && !str_contains($name, 'etf'))
                        <tr>
                            <td>{{ $dc->name }}</td>
                            <td>{{ number_format($deductionTotals[$dc->id] ?? 0, 2) }}</td>
                        </tr>
                        @endif
                        @endforeach
                        <tr style="font-weight:bold;">
                            <td>Total Deductions</td>
                            <td>{{ number_format($totalDeductions,2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>


    <div class="footer">
        <div class="signature">
            <div class="line"></div>
            <p>Cashier</p>
        </div>
    </div>
</body>

</html>