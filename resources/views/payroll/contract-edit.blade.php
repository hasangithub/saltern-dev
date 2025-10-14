@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', $batch->status.' Payroll - '.$batch->pay_period)
@section('content_header_subtitle', 'payrolls')

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

.table-compact th,
.table-compact td {
    padding: 2px 4px !important;
    /* reduce cell padding */
    font-size: 11.5px;
    /* smaller font */
    vertical-align: middle;
    /* align nicely */
}

.table-compact input.form-control {
    padding: 2px 4px;
    /* smaller input height */
    font-size: 11.5px;
    height: 22px;
    /* fixed compact height */
    line-height: 1;
}
</style>
<div class="container-fluid">

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <form method="POST" action="{{ route('payroll.batches.contractUpdate', $batch) }}">
        @csrf
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered align-middle  payroll-table table-compact">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width:120px;">Employee Name</th>
                            <th style="min-width:120px;">Day Salary</th>
                            <th style="min-width:80px;">Days</th>
                            <th style="min-width:80px;">Total Salary</th>
                            {{-- Dynamic earnings headers --}}
                            @foreach($earningComponents as $ec)
                            <th style="min-width:90px;" class="text-center">{{ $ec->name }}</th>
                            @endforeach
                            <th style="min-width:80px;">Hours</th>
                            <th style="min-width:90px;">Amounts</th>
                            <th style="min-width:200px;">8 Hours Duty Payments</th>
                            <th style="min-width:200px;">12 Hours Duty</th>
                            <th style="min-width:150px;">Poovarsan kuda 150 Payments</th>
                            <th style="min-width:150px;">Extra Hours</th>
                            <th class="text-right" style="min-width:90px;">Gross Salary</th>
                            {{-- Dynamic deductions headers --}}
                            @foreach($deductionComponents as $dc)
                            <th style="min-width: {{ in_array($dc->name, ['Loan', 'Festival Loan']) ? '230px' : '90px' }};"
                                class="text-center">
                                {{ $dc->name }}
                            </th>
                            @endforeach

                            <th class="text-right" style="min-width:90px;">Deductions</th>
                            <th class="text-right" style="min-width:90px;">Net Pay</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($batch->payrolls as $payroll)
                        @php
                        $emp = $payroll->employee;
                        @endphp
                        <tr>
                            <td>{{ $emp->user->name }}</td>

                            {{-- Basic Salary --}}
                            <td>
                                <input type="number" step="0.01" class="form-control text-right"
                                    name="payrolls[{{ $payroll->employee_id }}][day_salary]"
                                    value="{{ $payroll->day_salary }}" readonly>
                            </td>

                            <td>
                                <input type="number" step="0.01" class="form-control text-right day-input"
                                    name="payrolls[{{ $payroll->employee_id }}][worked_days]"
                                    value="{{ $payroll->worked_days }}">
                            </td>
                            <td>
                                <input type="number" step="0.01"
                                    name="payrolls[{{ $payroll->employee_id }}][total_salary]"
                                    value="{{ $payroll->day_salary * $payroll->worked_days }}" class="form-control"
                                    readonly>
                            </td>

                            {{-- Earnings --}}
                            @foreach($earningComponents as $ec)
                            @php
                            $earning = $payroll->earnings->firstWhere('component_id', $ec->id);
                            @endphp
                            <td class="table-secondary">
                                <input type="number" step="0.01" class="form-control text-right earning-input"
                                    name="earnings[{{ $payroll->employee_id }}][{{ $ec->id }}]"
                                    value="{{ $earning->amount ?? number_format($ec->default_amount, 2, '.', '') }}">
                            </td>
                            @endforeach

                            {{-- Overtime --}}
                            <td class="table-secondary">
                                <input type="number" step="0.1"
                                    name="payrolls[{{ $payroll->employee_id }}][overtime_hours]"
                                    value="{{ $payroll->overtime_hours }}" class="form-control">
                            </td>
                            <td class="table-secondary">
                                <input type="number" step="0.01"
                                    name="payrolls[{{ $payroll->employee_id }}][overtime_amount]"
                                    value="{{ $payroll->overtime_amount }}" class="form-control">
                            </td>

                            <td class="table-success">

                                <div style="display: flex; gap: 5px;">
                                    <input type="number" step="0.01"
                                        name="payrolls[{{ $emp->id }}][eight_hours_duty_hours]" class="form-control "
                                        value="{{$payroll->eight_hours_duty_hours}}">
                                    <input type="number" step="0.01"
                                        name="payrolls[{{ $emp->id }}][eight_hours_duty_amount]"
                                        class="form-control extra-earning-input"
                                        value="{{$payroll->eight_hours_duty_amount}}" readonly>
                                </div>
                            </td>

                            <td class="table-success">
                                <div style="display: flex; gap: 5px;">
                                    <input type="number" step="0.01" name="payrolls[{{ $emp->id }}][extra_half_days]"
                                        class="form-control " value="{{$payroll->extra_half_days}}">
                                    <input type="number" step="0.01"
                                        name="payrolls[{{ $emp->id }}][extra_half_days_amount]"
                                        class="form-control extra-earning-input" readonly
                                        value="{{$payroll->extra_half_days_amount}}">
                                </div>
                            </td>

                            <td class="table-success">
                                <div style="display: flex; gap: 5px;">
                                    <input type="number" step="0.01"
                                        name="payrolls[{{ $emp->id }}][poovarasan_kuda_allowance_150]"
                                        class="form-control " value="{{$payroll->poovarasan_kuda_allowance_150}}">
                                    <input type="number" step="0.01"
                                        name="payrolls[{{ $emp->id }}][poovarasan_kuda_allowance_150_amount]"
                                        class="form-control extra-earning-input" readonly
                                        value="{{$payroll->poovarasan_kuda_allowance_150_amount}}">
                                </div>

                            </td>

                            <td class="table-success">
                                <div style="display: flex; gap: 5px;">
                                    <input type="number" step="0.01" name="payrolls[{{ $emp->id }}][labour_hours]"
                                        class="form-control" value="{{$payroll->labour_hours}}">

                                    <input type="number" step="0.01" name="payrolls[{{ $emp->id }}][labour_amount]"
                                        class="form-control extra-earning-input" readonly>
                                </div>
                            </td>

                            {{-- Gross --}}
                            <td class="text-right gross-cell font-weight-bold">
                                {{ number_format($payroll->gross_earnings, 2) }}</td>

                            {{-- Deductions --}}
                            @foreach($deductionComponents as $dc)
                            @php
                            $deduction = $payroll->deductions->firstWhere('component_id', $dc->id);
                            $amount = $deduction->amount ?? 0;
                            $lowerName = strtolower($dc->name);
                            $balance = '';
                            @endphp
                            <td class="table-warning">
                                <div style="display: flex; gap: 5px;">
                                    @if(in_array($lowerName, ['loan', 'festival loan']))
                                    {{-- Loan / Festival Loan Dropdown --}}
                                    <select name="loan[{{ $payroll->employee_id }}][{{ $dc->id }}]"
                                        class="form-control loan-select">
                                        <option value="">-- Select {{ ucfirst(str_replace('_', ' ', $lowerName)) }} --
                                        </option>
                                        @foreach($emp->staffLoans->filter(function ($loan) use ($lowerName) {
                                        return $loan->loan_type == $lowerName
                                        && ($loan->is_migrated || $loan->voucher_id !== null);
                                        }) as $loan)
                                        @php
                                        $repayments = $loan->staffLoanRepayment->sum('amount');
                                        $balance = $loan->approved_amount - $repayments;
                                        @endphp
                                        <option value="{{ $loan->id }}" @if(optional($deduction)->loan_id == $loan->id)
                                            selected @endif
                                            data-balance="{{ $balance }}">
                                            {{ ucfirst($loan->loan_type) }} #{{ $loan->id }} - Balance:
                                            {{ number_format($balance, 2) }}
                                        </option>
                                        @endforeach
                                    </select>

                                    {{-- Repayment Amount --}}
                                    <input type="number" step="0.01"
                                        name="deductions[{{ $payroll->employee_id }}][{{ $dc->id }}]"
                                        class="form-control mt-1 loan-repayment deduction-input" value="{{ $amount }}">
                                    @else
                                    <input type="number" step="0.01" class="form-control text-right deduction-input"
                                        name="deductions[{{ $payroll->employee_id }}][{{ $dc->id }}]"
                                        value="{{ $amount }}">
                                    @endif
                                </div>
                            </td>

                            @endforeach

            </div>

            {{-- Totals --}}
            <td class="text-right ded-cell table-danger">{{ number_format($payroll->total_deductions, 2) }}</td>
            <td class="text-right net-cell font-weight-bold">{{ number_format($payroll->net_pay, 2) }}</td>
            </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th></th>
                    <th></th>
                    <th class="text-right total-basic">0.00</th>
                    {{-- Earnings totals --}}
                    @foreach($earningComponents as $ec)
                    <th class="text-right total-earning" data-component-id="{{ $ec->id }}">0.00</th>
                    @endforeach

                    <th class="text-right total-overtime-hours">0.00</th>
                    <th class="text-right total-overtime-amount">0.00</th>
                    <th class="text-right">0.00</th>
                    <th class="text-right">0.00</th>
                    <th class="text-right">0.00</th>
                    <th class="text-right">0.00</th>
                    <th class="text-right total-gross">0.00</th>

                    {{-- Deductions totals --}}
                    @foreach($deductionComponents as $dc)
                    <th class="text-right total-deduction" data-component-id="{{ $dc->id }}">0.00</th>
                    @endforeach

                    <th class="text-right total-deductions">0.00</th>
                    <th class="text-right total-net">0.00</th>
                </tr>
            </tfoot>

            </table>
        </div>

        <div class="card-footer d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-primary">Update Payroll</button>
        </div>
        <div class="card-footer">
            <div class="row payroll-summary">
                <!-- Left side: Earnings summary -->
                <div class="col-md-4">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th colspan="2">Earnings Summary</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Day Salary</td>
                                <td class="text-right summary-basic">0.00</td>
                            </tr>
                            @foreach($earningComponents as $ec)
                            <tr>
                                <td>{{ $ec->name }}</td>
                                <td class="text-right summary-earning" data-component-id="{{ $ec->id }}">0.00</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td>Overtime Amount</td>
                                <td class="text-right summary-overtime-amount">0.00</td>
                            </tr>
                            <tr>
                                <td>Extra Amount</td>
                                <td class="text-right summary-extra-amount">0.00</td>
                            </tr>
                            <tr class="fw-semibold">
                                <td>Total Earnings</td>
                                <td class="text-right summary-total-earnings">0.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Middle: Employee Deductions (EPF 8% only + others) -->
                <div class="col-md-4">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th colspan="2">Deductions Summary</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deductionComponents as $dc)
                            @php
                            $lower = strtolower($dc->name);
                            @endphp
                            @if(!str_contains($lower, 'etf') && !str_contains($lower, '12%'))
                            <tr>
                                <td>{{ $dc->name }}</td>
                                <td class="text-right summary-deduction" data-component-id="{{ $dc->id }}">0.00</td>
                            </tr>
                            @endif
                            @endforeach
                            <tr class="fw-semibold">
                                <td>Total Deductions</td>
                                <td class="text-right summary-total-deductions">0.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
</div>
</form>
{{-- Approve Payroll Batch Form --}}
@if($batch->status === 'draft')
<form action="{{ route('payroll.batches.approve', $batch->id) }}" method="POST"
    onsubmit="return confirm('Are you sure you want to approve this payroll batch? Once approved, it cannot be edited.')">
    @csrf
    <button type="submit" class="btn btn-success mt-3">
        <i class="fas fa-check-circle"></i> Approve Payroll Batch
    </button>
</form>
@else
<div class="mt-3">
    <span class="badge bg-success p-2">Approved</span>
</div>
@endif
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

    document.querySelectorAll('.loan-select').forEach(select => {
        select.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const balance = parseFloat(selectedOption.getAttribute('data-balance') || 0);

            // Find the repayment input in the same cell as this select
            const repaymentInput = this.closest('td').querySelector('.loan-repayment');

            if (repaymentInput) {
                repaymentInput.max = balance; // set max attribute
            }
        });
    });


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
    const payrollTemplate = "{{ $templateName }}";
    console.log("Template:", payrollTemplate);

    function recomputeTable() {
        let totalBasic = 0,
            totalOvertimeHours = 0,
            totalOvertimeAmount = 0,
            totalGross = 0,
            totalDeductions = 0,
            totalNet = 0;

        const earningTotals = {};
        const deductionTotals = {};

        table.querySelectorAll('tbody tr').forEach(tr => {
            const basic = parseFloat(tr.querySelector('input[name*="[day_salary]"]').value || 0);
            const daySalary = parseFloat(tr.querySelector('input[name*="[day_salary]"]').value || 0);

            const workedDays = parseFloat(tr.querySelector('input[name*="[worked_days]"]')?.value ||
                0);

            const oneDaySalary = daySalary;
            const totalSalary = workedDays * oneDaySalary;

            tr.querySelector('input[name*="[total_salary]"]').value = totalSalary.toFixed(2);

            const adjustedBase = totalSalary;
            const adjustedOneHourSalary = (payrollTemplate === 'Temporary Security') ?
                oneDaySalary / 12 :
                oneDaySalary / 8;

            const hourlyRate = parseFloat(adjustedOneHourSalary.toFixed(2));
            const extraWorkBasic = hourlyRate * 8;


            const mercDays = parseFloat(tr.querySelector('input[name*="[eight_hours_duty_hours]"]')
                ?.value ||
                0);
            const mercAmount = mercDays * extraWorkBasic;
            tr.querySelector('input[name*="[eight_hours_duty_amount]"]').value = mercAmount.toFixed(2);

            const extraHalfDays = parseFloat(tr.querySelector('input[name*="[extra_half_days]"]')
                ?.value || 0);

            const extraHalfAmount = extraHalfDays * extraWorkBasic / 2;
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

            const labourAmount = labourHours * hourlyRate;
            console.log(labourAmount);
            tr.querySelector('input[name*="[labour_amount]"]').value =
                labourAmount
                .toFixed(2);

            const overtimeHours = parseFloat(tr.querySelector('input[name*="[overtime_hours]"]')
                .value || 0);

            const overtimeAmount = ((oneDaySalary) * (3 / 16)) * overtimeHours;
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

        });

        // Update table footer
        table.querySelector('.total-basic').textContent = totalBasic.toFixed(2);
        table.querySelector('.total-overtime-hours').textContent = totalOvertimeHours.toFixed(2);
        table.querySelector('.total-overtime-amount').textContent = totalOvertimeAmount.toFixed(2);
        table.querySelector('.total-gross').textContent = totalGross.toFixed(2);
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

        updateFooterSummary();
    }

    function updateFooterSummary() {
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
            const merc = parseFloat(tr.querySelector('input[name*="[eight_hours_duty_amount]"]')
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

        document.querySelector('.summary-total-deductions').textContent = (totalDeds).toFixed(2);
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