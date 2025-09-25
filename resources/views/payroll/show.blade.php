@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', $batch->status.' Payroll - '.$batch->pay_period)
@section('content_header_subtitle', 'payrolls')
@section('page-buttons')
<a href="{{ route('payroll.batches.printSummary',  $batch->id) }}"
                   target="_blank" class="btn btn-sm btn-primary mr-2">
                    <i class="fas fa-file-alt"></i> Print Summary
                </a>
<a href="{{ route('payroll.batches.payslips', $batch->id) }}" class="btn btn-sm btn-success" target="_blank">
    <i class="bi bi-printer"></i> Print Payslips
</a>
@endsection
{{-- Content body: main page content --}}

@section('content_body')
<style>
.payroll-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
}

.payroll-table th.sticky-col,
.payroll-table td.sticky-col {
    position: sticky;
    left: 0;
    background: #fff !important;
    /* white background */
    z-index: 2;
    /* Ensure above other cells */
}

.payroll-table th.sticky-col {
    z-index: 3;
    /* Above sticky td */
}

/* Optional: add subtle shadow for better visibility */
.payroll-table .sticky-col {
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
}
</style>
<div class="container-fluid">

    <div class="col-md-12">
        <form method="GET" action="{{ route('payroll.batches.show', $batch->id) }}" class="mb-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <select name="department" class="form-control" onchange="this.form.submit()">
                        <option value="all" {{ $department == 'all' ? 'selected' : '' }}>All Departments</option>
                        <option value="office" {{ $department == 'office' ? 'selected' : '' }}>Office</option>
                        <option value="workshop" {{ $department == 'workshop' ? 'selected' : '' }}>Workshop</option>
                        <option value="security" {{ $department == 'security' ? 'selected' : '' }}>Security</option>
                    </select>
                </div>
                <div class="col-md-6 text-md-right mt-2 mt-md-0">
                    {{-- Print Button --}}
                    <a href="{{ route('payroll.batches.print', ['batch' => $batch->id, 'department' => $department]) }}"
                        target="_blank" class="btn btn-secondary">
                        <i class="fas fa-print"></i> Print
                    </a>
                </div>
            </div>
        </form>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle  payroll-table table-compact">
                    <thead>
                        <tr>
                            <th class="sticky-col" style="min-width:120px;">Employee Name</th>
                            <th style="width:20px;">EPF#</th>
                            <th style="width:40px;">Basic Salary</th>

                            {{-- Dynamic earnings headers --}}
                            @foreach($earningComponents as $ec)
                            <th style="width: 40px;">{{ $ec->name }}</th>
                            @endforeach

                            <th style="width:40px;">Hours</th>
                            <th style="width:40px;">Amounts</th>

                            <th style="width:40px;">Merch.Day</th>
                            <th style="width:40px;">Double Duty</th>
                            <th style="width:40px;">12 Hours Duty</th>
                            <th style="width:40px;">Poovarsan kuda 150 Payments</th>
                            <th style="width:40px;">Extra Hours</th>

                            <th style="width:40px;">Gross Salary</th>

                            {{-- Dynamic deductions headers --}}
                            @foreach($deductionComponents as $dc)
                            <th style="width:40px;">{{ $dc->name }}</th>
                            @endforeach

                            <th style="width:40px;">EPF 8%</th>
                            <th style="width:40px;">No Pay</th>

                            <th style="width:40px;">Deductions</th>
                            <th style="width:40px;">Net Pay</th>
                            <th style="width:90px;">Signature</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($batch->payrolls as $payroll)
                        @php $emp = $payroll->employee;
                        $extraEarnings = $payroll->mercantile_days_amount + $payroll->extra_full_days_amount +
                        $payroll->extra_half_days_amount + $payroll->poovarasan_kuda_allowance_150_amount +
                        $payroll->labour_amount;
                        @endphp

                        <tr>
                            <td class="sticky-col">{{ $emp->user->name }}</td>
                            <td>{{ $emp->epf_number }}</td>
                            <td class="text-right">{{ number_format($payroll->basic_salary,2) }}</td>

                            @foreach($earningComponents as $ec)
                            @php $earning = $payroll->earnings->firstWhere('component_id', $ec->id); @endphp
                            <td class="text-right">{{ number_format($earning->amount ?? 0,2) }}</td>
                            @endforeach

                            <td class="text-right">{{ number_format($payroll->overtime_hours ?? 0,1) }}</td>
                            <td class="text-right">{{ number_format($payroll->overtime_amount ?? 0,2) }}</td>
                            <td class="text-right">{{ number_format($payroll->mercantile_days_amount ?? 0,2) }}</td>
                            <td class="text-right">{{ number_format($payroll->extra_full_days_amount ?? 0,2) }}</td>
                            <td class="text-right">{{ number_format($payroll->extra_half_days_amount ?? 0,2) }}</td>
                            <td class="text-right">
                                {{ number_format($payroll->poovarasan_kuda_allowance_150_amount ?? 0,2) }}
                            </td>
                            <td class="text-right">{{ number_format($payroll->labour_amount ?? 0,2) }}</td>
                            <td class="text-right">{{ number_format($payroll->gross_earnings + $extraEarnings ?? 0,2) }}
                            </td>

                            @foreach($deductionComponents as $dc)
                            @php $deduction = $payroll->deductions->firstWhere('component_id', $dc->id); @endphp
                            <td class="text-right">{{ number_format($deduction->amount ?? 0,2) }}</td>
                            @endforeach

                            <td class="text-right">{{ number_format($payroll->epf_employee ?? 0,2) }}</td>
                            <td class="text-right">{{ number_format($payroll->no_pay ?? 0,2) }}</td>

                            <td class="text-right">{{ number_format($payroll->total_deductions ?? 0,2) }}</td>
                            <td class="text-right fw-semibold">{{ number_format($payroll->net_pay ?? 0,2) }}</td>
                            <td></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tr>
                        <td style="width:40px;"></td>
                        <td style="width:20px;"></td>
                        <td style="width:40px;">{{ number_format($batch->payrolls->sum('basic_salary'),2) }}</td>

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
                            {{ number_format($batch->payrolls->sum('mercantile_days_amount'),2) }}</td>
                        <td style="width:40px;" class="text-right">
                            {{ number_format($batch->payrolls->sum('extra_full_days_amount'),2) }}</td>
                        <td style="width:40px;" class="text-right">
                            {{ number_format($batch->payrolls->sum('extra_half_days_amount'),2) }}</td>
                        <td style="width:40px;" class="text-right">
                            {{ number_format($batch->payrolls->sum('poovarasan_kuda_allowance_150_amount'),2) }}</td>
                        <td style="width:40px;" class="text-right">
                            {{ number_format($batch->payrolls->sum('labour_amount'),2) }}
                        </td>

                        <td style="width:40px;" class="text-right">
                            {{ number_format($batch->payrolls->sum('gross_earnings') +  $batch->payrolls->sum('mercantile_days_amount') +
        $batch->payrolls->sum('extra_full_days_amount') +
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
                            {{ number_format($batch->payrolls->sum('epf_employee'),2) }}
                        </td>
                        <td style="width:40px;" class="text-right">
                            {{ number_format($batch->payrolls->sum('no_pay'),2) }}</td>

                        <td style="width:40px;" class="text-right">
                            {{ number_format($batch->payrolls->sum('total_deductions'),2) }}</td>
                        <td style="width:40px;" class="text-right">
                            {{ number_format($batch->payrolls->sum('net_pay'),2) }}</td>
                        <td style="width:90px;" class="text-right"></td>

                    </tr>
                </table>
            </div>
        </div>
    </div>



    {{-- === Summary Tables (side-by-side using floats) === --}}
    <div class="row">
        {{-- Earnings Summary --}}
        <div class="col-md-4">
            <table class="table">
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
        </div>
        <div class="col-md-4">
            {{-- Deductions Summary --}}
            <table class="table">
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
        </div>
        <div class="col-md-4">
            {{-- Employer Contributions --}}
            <table class="table">
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
        </div>
    </div>
</div>
@stop

{{-- Push extra CSS --}}

@push('css')
{{-- Add here extra stylesheets --}}
{{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@endpush

{{-- Push extra scripts --}}

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#payroll-form input').attr('readonly', 'readonly');
    document.querySelectorAll('.loan-select').forEach(function(select) {
        const repaymentInput = select.closest('td').querySelector('.loan-repayment');

        // Enable/disable on page load
        repaymentInput.disabled = !select.value;

        // Enable/disable when selection changes
        select.addEventListener('change', function() {
            repaymentInput.disabled = !this.value;
            if (!this.value) repaymentInput.value = ''; // Clear if no loan selected
        });
    });
    // Main payroll table
    const table = document.querySelector('.payroll-table');

    function recomputeTable() {
        let totalBasic = 0,
            totalOvertimeHours = 0,
            totalOvertimeAmount = 0,
            totalGross = 0,
            totalDeductions = 0,
            totalNet = 0;

        let totalEPF12 = 0;
        let totalEPF8 = 0;
        let totalETF3 = 0;

        const earningTotals = {};
        const deductionTotals = {};

        table.querySelectorAll('tbody tr').forEach(tr => {
            const basic = parseFloat(tr.querySelector('input[name*="[basic_salary]"]').value || 0);

            const noPayDays = parseFloat(tr.querySelector('input[name*="[no_pay_days]"]')?.value ||
                0);

            const oneDaySalary = basic / 30;

            const noPayAmount = noPayDays * oneDaySalary;

            tr.querySelector('input[name*="[no_pay]"]').value = noPayAmount.toFixed(2);

            const noPayInput = tr.querySelector('input[name*="[no_pay]"]');
            let noPay = parseFloat(noPayInput?.value || 0);

            const adjustedBase = basic - noPay;
            const adjustedOneDaySalary = adjustedBase / 30;

            const epf = adjustedBase * 0.08;
            tr.querySelector('input[name*="[epf]"]').value = epf.toFixed(2);

            const mercDays = parseFloat(tr.querySelector('input[name*="[mercantile_days]"]')?.value ||
                0);
            const mercAmount = mercDays * oneDaySalary;
            tr.querySelector('input[name*="[mercantile_days_amount]"]').value = mercAmount.toFixed(2);

            const extraFullDays = parseFloat(tr.querySelector('input[name*="[extra_full_days]"]')
                ?.value || 0);
            const extraFullAmount = extraFullDays * oneDaySalary;
            tr.querySelector('input[name*="[extra_full_days_amount]"]').value = extraFullAmount.toFixed(
                2);

            const extraHalfDays = parseFloat(tr.querySelector('input[name*="[extra_half_days]"]')
                ?.value || 0);
            const extraHalfAmount = extraHalfDays * oneDaySalary / 2;
            tr.querySelector('input[name*="[extra_half_days_amount]"]').value = extraHalfAmount.toFixed(
                2);

            const poovarasanDays = parseFloat(tr.querySelector(
                    'input[name*="[poovarasan_kuda_allowance_150]"]')
                ?.value || 0);

            const poovarasanAmount = poovarasanDays * 150;
            tr.querySelector('input[name*="[poovarasan_kuda_allowance_150_amount]"]').value =
                poovarasanAmount
                .toFixed(2);

            const labourHours = parseFloat(tr.querySelector(
                    'input[name*="[labour_hours]"]')
                ?.value || 0);

            const labourAmount = labourHours * (basic / 240);
            console.log(labourAmount);
            tr.querySelector('input[name*="[labour_amount]"]').value =
                labourAmount
                .toFixed(2);

            const overtimeHours = parseFloat(tr.querySelector('input[name*="[overtime_hours]"]')
                .value || 0);

            const overtimeAmount = ((adjustedBase / 30) * (3 / 16)) * overtimeHours;
            tr.querySelector('input[name*="[overtime_amount]"]').value =
                overtimeAmount.toFixed(2);

            let gross = adjustedBase + overtimeAmount;
            let ded = 0;
            let extraWork = 0;

            // Earnings
            tr.querySelectorAll('.earning-input').forEach(input => {
                const val = parseFloat(input.value || 0);
                const compId = input.name.match(/\[(\d+)\]$/)?. [1];
                if (compId) {
                    earningTotals[compId] = (earningTotals[compId] || 0) + val;
                }
                gross += val;
            });

            tr.querySelectorAll('.extra-earning-input').forEach(input => {
                const val = parseFloat(input.value || 0);
                extraWork += val;
            });

            // Deductions
            tr.querySelectorAll('.deduction-input').forEach(input => {
                const val = parseFloat(input.value || 0);
                const compId = input.name.match(/\[(\d+)\]$/)?. [1];
                if (compId) {
                    deductionTotals[compId] = (deductionTotals[compId] || 0) + val;
                }
                ded += val;
            });

            tr.querySelector('.gross-cell').textContent = gross.toFixed(2);
            tr.querySelector('.ded-cell').textContent = ded.toFixed(2);
            tr.querySelector('.net-cell').textContent = (extraWork + gross - ded).toFixed(2);

            totalBasic += adjustedBase;
            totalOvertimeHours += overtimeHours;
            totalOvertimeAmount += overtimeAmount;
            totalGross += gross;
            totalDeductions += ded;
            totalNet += (gross - ded);

            totalEPF12 += adjustedBase * 0.12;
            totalEPF8 += adjustedBase * 0.08;
            totalETF3 += adjustedBase * 0.03;
        });

        // Update table footer
        table.querySelector('.total-basic').textContent = totalBasic.toFixed(2);
        table.querySelector('.total-overtime-hours').textContent = totalOvertimeHours.toFixed(2);
        table.querySelector('.total-overtime-amount').textContent = totalOvertimeAmount.toFixed(2);
        table.querySelector('.total-gross').textContent = totalGross.toFixed(2);
        table.querySelector('.total-epf8').textContent = totalEPF8.toFixed(2);
        table.querySelector('.total-deductions').textContent = totalDeductions.toFixed(2);
        table.querySelector('.total-net').textContent = totalNet.toFixed(2);

        table.querySelectorAll('.total-earning').forEach(th => {
            const compId = th.dataset.componentId;
            th.textContent = (earningTotals[compId] || 0).toFixed(2);
        });

        table.querySelectorAll('.total-deduction').forEach(th => {
            const compId = th.dataset.componentId;
            th.textContent = (deductionTotals[compId] || 0).toFixed(2);
        });

        updateFooterSummary(totalEPF12, totalETF3, totalEPF8);
    }

    function updateFooterSummary(totalEPF12, totalETF3, totalEPF8) {
        // Left side: Basic + Earnings + Overtime
        const totalBasic = parseFloat(table.querySelector('.total-basic').textContent || 0);
        const totalOvertimeAmount = parseFloat(table.querySelector('.total-overtime-amount').textContent || 0);

        document.querySelector('.summary-basic').textContent = totalBasic.toFixed(2);
        document.querySelector('.summary-overtime-amount').textContent = totalOvertimeAmount.toFixed(2);

        let grandEarnings = totalBasic + totalOvertimeAmount;

        document.querySelectorAll('.summary-earning').forEach(td => {
            const compId = td.dataset.componentId;
            const val = parseFloat(document.querySelector(
                `.total-earning[data-component-id="${compId}"]`).textContent || 0);
            td.textContent = val.toFixed(2);
            grandEarnings += val;
        });

        // Add extra amounts: mercantile, extra full/half days, Poovarasan, labour
        let totalExtraAmounts = 0;
        table.querySelectorAll('tbody tr').forEach(tr => {
            const merc = parseFloat(tr.querySelector('input[name*="[mercantile_days_amount]"]')
                ?.value || 0);
            const full = parseFloat(tr.querySelector('input[name*="[extra_full_days_amount]"]')
                ?.value || 0);
            const half = parseFloat(tr.querySelector('input[name*="[extra_half_days_amount]"]')
                ?.value || 0);
            const poovarasan = parseFloat(tr.querySelector(
                    'input[name*="[poovarasan_kuda_allowance_150_amount]"]')
                ?.value || 0);
            const labour = parseFloat(tr.querySelector('input[name*="[labour_amount]"]')?.value || 0);

            totalExtraAmounts += merc + full + half + poovarasan + labour;
        });

        document.querySelector('.summary-extra-amount').textContent = totalExtraAmounts.toFixed(2);
        grandEarnings += totalExtraAmounts;

        document.querySelector('.summary-total-earnings').textContent = grandEarnings.toFixed(2);

        // Right side: Deductions excluding EPF12% and ETF
        let totalDeds = 0;
        document.querySelectorAll('.summary-deduction').forEach(td => {
            const compId = td.dataset.componentId;
            const val = parseFloat(document.querySelector(
                `.total-deduction[data-component-id="${compId}"]`).textContent || 0);
            td.textContent = val.toFixed(2);
            totalDeds += val;
        });
        document.querySelector('.summary-total-deductions').textContent = (totalDeds + totalEPF8).toFixed(2);
        document.querySelector('.summary-epf8').textContent = totalEPF8.toFixed(2);
        // Employer contributions
        document.querySelector('.summary-epf12').textContent = totalEPF12.toFixed(2);
        document.querySelector('.summary-etf3').textContent = totalETF3.toFixed(2);
        document.querySelector('.summary-total-employer').textContent = (totalEPF12 + totalETF3).toFixed(2);
    }


    // Recompute on any input in this table only
    table.querySelectorAll('tbody input').forEach(input => {
        input.addEventListener('input', recomputeTable);
    });

    // Initial totals
    recomputeTable();
});
</script>
@endpush