<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Payroll Print</title>
    <style>
    /* Page setup */
    @page {
        size: legal landscape;
        margin: 2mm;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 9px;
        margin: 0;
        color: #000;
    }

    /* Main payroll table */
    .table-main {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
        /* table-layout: fixed; */
    }

    .table-main th,
    .table-main td {
        border: 1px solid #000;
        padding: 2px;
        vertical-align: middle;
    }

    .table-main th {
        background: #f2f2f2;
        font-weight: 600;
        font-size: 9px;
    }

    .text-right {
        text-align: right;
    }

    .fw-semibold {
        font-weight: 700;
    }

    /* Summary tables container (float approach for Dompdf) */
    .summary-wrapper {
        width: 100%;
        margin-top: 10px;
        overflow: hidden;
        /* clearfix */
    }

    .summary-table {
        float: left;
        width: 32%;
        border-collapse: collapse;
        margin-right: 2%;
        box-sizing: border-box;
        font-size: 9px;
    }

    .summary-table:last-child {
        margin-right: 0;
    }

    .summary-table th,
    .summary-table td {
        border: 1px solid #000;
        padding: 1px;
    }

    .summary-table thead th {
        background: #f2f2f2;
        font-weight: 600;
    }

    /* Ensure tables do not break rows across pages */
    tr {
        page-break-inside: avoid;
    }

    table {
        page-break-inside: avoid;
    }

    /* If necessary, reduce spacing so content fits */
    .small {
        font-size: 9px;
    }
    </style>
</head>

<body>
    <div class="payroll-wrapper">
        {{-- === Heading === --}}
        <div style="text-align:center; margin-bottom:15px;">
            <h2 style="margin:0; font-size:12px; font-weight:bold; text-transform:uppercase;">
                PUTTALAM SALT PRODUCERS WELFARE SOCIETY LTD
            </h2>
            <p style="margin:0; font-size:10px;">Payroll Report - {{ $batch->pay_period }} </p>
            @if(isset($department))
            <p style="margin:0; font-size:10px;">
                Department: {{ ucfirst($department) }}
            </p>
            @else
            <p style="margin:0; font-size:10px;">
                Department: All
            </p>
            @endif
        </div>
        {{-- === Main Table === --}}
        @php
        $isTemporarySecurity =  $batch->payrollTemplate->name === 'Temporary Security';
        @endphp
        {{ $batch->payrollTemplate->name }}
        <table class="table-main small">
            <thead>
                <tr>
                    <th style="width:40px;">Employee Name</th>
                    <th style="width:40px;">Day Salary</th>

                    {{-- Dynamic earnings headers --}}
                    @foreach($earningComponents as $ec)
                    <th style="width: 40px;">{{ $ec->name }}</th>
                    @endforeach

                    <th style="width:40px;">Hours</th>
                    <th style="width:40px;">Amounts</th>

                    <th style="width:40px;">Total Salary</th>
                    @if($isTemporarySecurity)
                    <th style="width:40px;">8 Hours Duty</th>
                    @endif
                    <th style="width:40px;">12 Hours Duty</th>
                    @if($isTemporarySecurity)    
                    <th style="width:40px;">Poovarsan kuda 150 Payments</th>
                    @endif
                    <th style="width:40px;">Extra Hours</th>

                    <th style="width:40px;">Gross Salary</th>

                    {{-- Dynamic deductions headers --}}
                    @foreach($deductionComponents as $dc)
                    <th style="width:40px;">{{ $dc->name }}</th>
                    @endforeach

                    <th style="width:40px;">Deductions</th>
                    <th style="width:40px;">Net Pay</th>
                    <th style="width:90px;">Signature</th>
                </tr>
            </thead>
            <tbody>
                @foreach($batch->payrolls as $payroll)
                @php $emp = $payroll->employee;
                $extraEarnings = $payroll->eight_hours_duty_amount +
                $payroll->extra_half_days_amount + $payroll->poovarasan_kuda_allowance_150_amount +
                $payroll->labour_amount;
                @endphp

                <tr>
                    <td>{{ $emp->user->name }}</td>

                    <td class="text-right">{{ number_format($payroll->day_salary,2) }}</td>

                    @foreach($earningComponents as $ec)
                    @php $earning = $payroll->earnings->firstWhere('component_id', $ec->id); @endphp
                    <td class="text-right">{{ number_format($earning->amount ?? 0,2) }}</td>
                    @endforeach

                    <td class="text-right">{{ number_format($payroll->overtime_hours ?? 0,1) }}</td>
                    <td class="text-right">{{ number_format($payroll->overtime_amount ?? 0,2) }}</td>
                    <td class="text-right">{{ number_format($payroll->worked_days_amount ?? 0,2) }}</td>
                    @if($isTemporarySecurity)
                    <td class="text-right">{{ number_format($payroll->eight_hours_duty_amount ?? 0,2) }}</td>
                    @endif
                    <td class="text-right">{{ number_format($payroll->extra_half_days_amount ?? 0,2) }}</td>
                    @if($isTemporarySecurity)    
                    <td class="text-right">{{ number_format($payroll->poovarasan_kuda_allowance_150_amount ?? 0,2) }}
                    </td>
                    @endif
                    <td class="text-right">{{ number_format($payroll->labour_amount ?? 0,2) }}</td>
                    <td class="text-right">{{ number_format($payroll->gross_earnings + $extraEarnings ?? 0,2) }}</td>

                    @foreach($deductionComponents as $dc)
                    @php $deduction = $payroll->deductions->firstWhere('component_id', $dc->id); @endphp
                    <td class="text-right">{{ number_format($deduction->amount ?? 0,2) }}</td>
                    @endforeach



                    <td class="text-right">{{ number_format($payroll->total_deductions ?? 0,2) }}</td>
                    <td class="text-right fw-semibold">{{ number_format($payroll->net_pay ?? 0,2) }}</td>
                    <td></td>
                </tr>
                @endforeach
            </tbody>
            <tr>
                <td style="width:40px;"></td>
                <td style="width:40px;" class="text-right">
                </td>

                {{-- Dynamic earnings headers --}}
                @foreach($earningComponents as $ec)
                <td style="width: 40px;" class="text-right">
                    {{ number_format(
                    $batch->payrolls->sum(function($payroll) use ($ec) {
                        $earning = $payroll->earnings->firstWhere('component_id', $ec->id);
                        return $earning->amount ?? 0;
                    }), 2)
                }}
                </td>
                @endforeach

                <td style="width:40px;" class="text-right">
                    {{ number_format($batch->payrolls->sum('overtime_hours'),2) }}</td>
                <td style="width:40px;" class="text-right">
                    {{ number_format($batch->payrolls->sum('overtime_amount'),2) }}</td>

                <td style="width:40px;" class="text-right">
                    {{ number_format($batch->payrolls->sum('worked_days_amount'),2) }}</td>
                @if($isTemporarySecurity)
                <td style="width:40px;" class="text-right">
                    {{ number_format($batch->payrolls->sum('eight_hours_duty_amount'),2) }}</td>
                @endif
                <td style="width:40px;" class="text-right">
                    {{ number_format($batch->payrolls->sum('extra_half_days_amount'),2) }}</td>
                @if($isTemporarySecurity)    
                <td style="width:40px;" class="text-right">
                    {{ number_format($batch->payrolls->sum('poovarasan_kuda_allowance_150_amount'),2) }}</td>
                @endif
                <td style="width:40px;" class="text-right">{{ number_format($batch->payrolls->sum('labour_amount'),2) }}
                </td>

                <td style="width:40px;" class="text-right">
                    {{ number_format($batch->payrolls->sum('gross_earnings') +  $batch->payrolls->sum('eight_hours_duty_amount') +
        $batch->payrolls->sum('extra_half_days_amount') +
        $batch->payrolls->sum('poovarasan_kuda_allowance_150_amount') +
        $batch->payrolls->sum('labour_amount'),2) }}</td>

                {{-- Dynamic deductions headers --}}
                @foreach($deductionComponents as $dc)
                <td class="text-right">
                    {{ number_format(
                    $batch->payrolls->sum(function($payroll) use ($dc) {
                        $deduction = $payroll->deductions->firstWhere('component_id', $dc->id);
                        return $deduction->amount ?? 0;
                    }), 2)
                }}
                </td>
                @endforeach

                <td style="width:40px;" class="text-right">
                    {{ number_format($batch->payrolls->sum('total_deductions'),2) }}</td>
                <td style="width:40px;" class="text-right">{{ number_format($batch->payrolls->sum('net_pay'),2) }}</td>
                <td style="width:90px;" class="text-right"></td>

            </tr>
        </table>

        {{-- === Summary Tables (side-by-side using floats) === --}}
        <div class="summary-wrapper">
            {{-- Earnings Summary --}}
            <table class="summary-table small">
                <thead>
                    <tr>
                        <th colspan="2">Earnings Summary</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Salary </td>
                        <td class="text-right">
                            {{ number_format($batch->payrolls->sum('worked_days_amount'),2) }}
                        </td>
                    </tr>

                    @foreach($earningComponents as $ec)
                    <tr>
                        <td>{{ $ec->name }}</td>
                        <td class="text-right">
                            {{ number_format($batch->payrolls->sum(fn($p) => optional($p->earnings->firstWhere('component_id',$ec->id))->amount),2) }}
                        </td>
                    </tr>
                    @endforeach

                    <tr>
                        <td>Overtime</td>
                        <td class="text-right">{{ number_format($batch->payrolls->sum('overtime_amount'),2) }}</td>
                    </tr>

                    <tr>
                        <td>Extra</td>
                        <td class="text-right">
                            {{ number_format($batch->payrolls->sum(fn($p) => $p->eight_hours_duty_amount  + $p->extra_half_days_amount + $p->poovarasan_kuda_allowance_150_amount + $p->labour_amount),2) }}
                        </td>
                    </tr>

                    <tr class="fw-semibold">
                        <td>Total</td>
                        <td class="text-right">
                            {{ number_format($batch->payrolls->sum(fn($p) => $p->eight_hours_duty_amount  + $p->extra_half_days_amount + $p->poovarasan_kuda_allowance_150_amount + $p->labour_amount) + $batch->payrolls->sum('gross_earnings'),2) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- Deductions Summary --}}
            <table class="summary-table small">
                <thead>
                    <tr>
                        <th colspan="2">Deductions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deductionComponents as $dc)
                    <tr>
                        <td>{{ $dc->name }}</td>
                        <td class="text-right">
                            {{ number_format($batch->payrolls->sum(fn($p) => optional($p->deductions->firstWhere('component_id',$dc->id))->amount),2) }}
                        </td>
                    </tr>
                    @endforeach

                    <tr class="fw-semibold">
                        <td>Total</td>
                        <td class="text-right">{{ number_format($batch->payrolls->sum('total_deductions'),2) }}</td>
                    </tr>
                </tbody>
            </table>

            {{-- Employer Contributions --}}
            <table class="summary-table small" style="line-height: 18.5px;">
                <thead>
                    <tr>
                        <th colspan="2">Summary</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Earnings</td>
                        <td class="text-right">
                            {{ number_format($batch->payrolls->sum(fn($p) => $p->eight_hours_duty_amount  + $p->extra_half_days_amount + $p->poovarasan_kuda_allowance_150_amount + $p->labour_amount) + $batch->payrolls->sum('gross_earnings'),2) }}
                        </td>
                    </tr>
                    <tr>
                        <td>Total Deductions</td>
                        <td class="text-right">{{ number_format($batch->payrolls->sum('total_deductions'),2) }}</td>
                    </tr>

                    <tr>
                        <td colspan="2"></td>
                    </tr>

                    <tr class="fw-semibold">
                        <td>Balance</td>
                        <td class="text-right">
                            {{ number_format(($batch->payrolls->sum('net_pay')),2) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <div style="clear: both;"></div>
        </div>
        <table style="width:100%; margin-top:50px; text-align:center; border:0;">
            <tr>
                <td style="width:33%; padding-top:50px;">
                    -----------------------------<br>
                    <strong>Prepared By</strong>
                </td>
                <td style="width:33%; padding-top:50px;">
                    -----------------------------<br>
                    <strong>Manager</strong>
                </td>
                <td style="width:33%; padding-top:50px;">
                    -----------------------------<br>
                    <strong>President</strong>
                </td>
            </tr>
        </table>

    </div>
</body>

</html>