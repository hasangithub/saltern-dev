<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Payroll Print</title>
    <style>
    /* Page setup */
    @page {
        size: a4 landscape;
        margin: 10mm;
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
        padding: 4px;
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
        padding: 4px;
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
                        <td>Basic Salary</td>
                        <td class="text-right">{{ number_format($batch->payrolls->sum('basic_salary'),2) }}</td>
                    </tr>

                    <tr>
                        <td>Salary (after deduction)</td>
                        <td class="text-right">
                            {{ number_format($batch->payrolls->sum('basic_salary') - $batch->payrolls->sum('no_pay'),2) }}
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
                            {{ number_format($batch->payrolls->sum(fn($p) => $p->mercantile_days_amount + $p->extra_full_days_amount + $p->extra_half_days_amount + $p->poovarasan_kuda_allowance_150_amount + $p->labour_amount),2) }}
                        </td>
                    </tr>

                    <tr class="fw-semibold">
                        <td>Total</td>
                        <td class="text-right">
                            {{ number_format($batch->payrolls->sum(fn($p) => $p->mercantile_days_amount + $p->extra_full_days_amount + $p->extra_half_days_amount + $p->poovarasan_kuda_allowance_150_amount + $p->labour_amount) + $batch->payrolls->sum('gross_earnings'),2) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- Deductions Summary --}}
            <table class="summary-table small" style="line-height: 13px;">
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

                    <tr>
                        <td>EPF 8%</td>
                        <td class="text-right">{{ number_format($batch->payrolls->sum('epf_employee'),2) }}</td>
                    </tr>
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
                        <td>EPF 12%</td>
                        <td class="text-right">{{ number_format($batch->payrolls->sum('epf_employer'),2) }}</td>
                    </tr>
                    <tr>
                        <td>EPF 8%</td>
                        <td class="text-right">{{ number_format($batch->payrolls->sum('epf_employee'),2) }}</td>
                    </tr>
                    <tr class="fw-semibold">
                        <td>Total</td>
                        <td class="text-right">
                            {{ number_format($batch->payrolls->sum('epf_employer') + $batch->payrolls->sum('epf_employee'),2) }}
                        </td>
                    </tr>
                    <tr>
                        <td>ETF 3%</td>
                        <td class="text-right">{{ number_format($batch->payrolls->sum('etf'),2) }}</td>
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
                <td style="width:33%; padding-top:60px;">
                    -----------------------------<br>
                    <strong>Prepared By</strong>
                </td>
                <td style="width:33%; padding-top:60px;">
                    -----------------------------<br>
                    <strong>Manager</strong>
                </td>
                <td style="width:33%; padding-top:60px;">
                    -----------------------------<br>
                    <strong>President</strong>
                </td>
            </tr>
        </table>

    </div>
</body>

</html>