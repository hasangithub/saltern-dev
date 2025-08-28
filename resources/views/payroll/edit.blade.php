@extends('layout.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Welcome')
@section('content_header_title', 'payrolls')
@section('content_header_subtitle', 'payrolls')

{{-- Content body: main page content --}}

@section('content_body')

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Build Payroll — {{ $batch->pay_period }}</h4>
            <small class="text-muted">Status: {{ ucfirst($batch->status) }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('payroll.batches.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <form method="POST" action="{{ route('payroll.batches.update', $batch) }}">
        @csrf
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered align-middle  payroll-table">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width:120px;">Employee Name</th>
                            <th style="min-width:120px;">Basic Salary</th>
                            {{-- Dynamic earnings headers --}}
                            @foreach($earningComponents as $ec)
                            <th style="min-width:120px;" class="text-center">{{ $ec->name }}</th>
                            @endforeach
                            <th style="min-width:120px;">Hours</th>
                            <th style="min-width:120px;">Amounts</th>
                            <th class="text-end" style="min-width:120px;">Gross Salary</th>
                            {{-- Dynamic deductions headers --}}
                            @foreach($deductionComponents as $dc)
                            <th style="min-width:200px;" class="text-center">{{ $dc->name }}</th>
                            @endforeach
                            <th style="min-width:120px;">EPF</th>
                            <th style="min-width:120px;">No Pay</th>
                            <th style="min-width:250px;">Merch.Day Payments</th>
                            <th style="min-width:250px;">Extra 1 Day Payments</th>
                            <th style="min-width:250px;">Extra 12 Hours Payments</th>
                            <th style="min-width:250px;">Poovarsan kuda 150 Payments</th>
                            <th style="min-width:120px;">Lab. Pay</th>
                            <th class="text-end" style="min-width:120px;">Deductions</th>
                            <th class="text-end" style="min-width:120px;">Net Pay</th>
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
                                <input type="number" step="0.01" class="form-control text-end"
                                    name="payrolls[{{ $payroll->employee_id }}][basic_salary]"
                                    value="{{ $payroll->basic_salary }}" readonly>
                            </td>

                            {{-- Earnings --}}
                            @foreach($earningComponents as $ec)
                            @php
                            $earning = $payroll->earnings->firstWhere('component_id', $ec->id);
                            @endphp
                            <td>
                                <input type="number" step="0.01" class="form-control text-end earning-input"
                                    name="earnings[{{ $payroll->employee_id }}][{{ $ec->id }}]"
                                    value="{{ $earning->amount ?? number_format($ec->default_amount, 2, '.', '') }}">
                            </td>
                            @endforeach

                            {{-- Overtime --}}
                            <td>
                                <input type="number" step="0.1"
                                    name="payrolls[{{ $payroll->employee_id }}][overtime_hours]"
                                    value="{{ $payroll->overtime_hours }}">
                            </td>
                            <td>
                                <input type="number" name="payrolls[{{ $payroll->employee_id }}][overtime_amount]"
                                    value="{{ $payroll->overtime_amount }}">
                            </td>

                            {{-- Gross --}}
                            <td class="text-end gross-cell">{{ number_format($payroll->gross_earnings, 2) }}</td>

                            {{-- Deductions --}}
                            @foreach($deductionComponents as $dc)
                            @php
                            $deduction = $payroll->deductions->firstWhere('component_id', $dc->id);
                            $amount = $deduction->amount ?? 0;
                            @endphp
                            <td>
                                @if(strtolower($dc->name) === 'loan')
                                {{-- Loan Dropdown --}}
                                <select name="loan[{{ $payroll->employee_id }}]" class="form-control loan-select">
                                    <option value="">-- Select Loan --</option>
                                    @foreach($emp->staffLoans->filter(fn($loan) => $loan->voucher_id !== null) as $loan)
                                    @php
                                    $repayments = $loan->staffLoanRepayment->sum('amount'); // sum all repayments
                                    $balance = $loan->approved_amount - $repayments; // remaining balance
                                    @endphp
                                    <option value="{{ $loan->id }}" @if(optional($deduction)->loan_id == $loan->id)
                                        selected @endif
                                        data-balance="{{ $loan->balance }}">
                                        Loan #{{ $loan->id }} - Balance: {{ number_format($balance, 2) }}
                                    </option>
                                    @endforeach
                                </select>

                                {{-- Repayment Amount --}}
                                <input type="number" step="0.01"
                                    name="deductions[{{ $payroll->employee_id }}][{{ $dc->id }}]"
                                    class="form-control mt-1 loan-repayment deduction-input" value="{{ $amount }}">
                                @else
                                <input type="number" step="0.01" class="form-control text-end deduction-input"
                                    name="deductions[{{ $payroll->employee_id }}][{{ $dc->id }}]" value="{{ $amount }}">
                                @endif
                            </td>
                            @endforeach

                            <td><input type="number" step="0.01" name="payrolls[{{ $emp->id }}][epf]"
                                    class="form-control deduction-input" value="{{ $emp->base_salary * 0.08}}" readonly>
                            </td>
                            <td><input type="number" step="0.01" name="payrolls[{{ $emp->id }}][no_pay]"
                                    class="form-control no-pay-input"></td>

                            <td>

                                <div style="display: flex; gap: 5px;">
                                    <input type="number" step="0.01" name="payrolls[{{ $emp->id }}][mercantile_days]"
                                        class="form-control " value="{{$payroll->mercantile_days}}">
                                    <input type="number" step="0.01"
                                        name="payrolls[{{ $emp->id }}][mercantile_days_amount]"
                                        class="form-control earning-input" value="{{$payroll->mercantile_days_amount}}" readonly>
                                </div>
                            </td>

                            <td>
                                <div style="display: flex; gap: 5px;">
                                    <input type="number" step="0.01" name="payrolls[{{ $emp->id }}][extra_full_days]"
                                        class="form-control " value="{{$payroll->extra_full_days}}">
                                    <input type="number" step="0.01"
                                        name="payrolls[{{ $emp->id }}][extra_full_days_amount]"
                                        class="form-control earning-input" value="{{$payroll->extra_full_days_amount}}" readonly>
                                </div>
                            </td>

                            <td>
                                <div style="display: flex; gap: 5px;">
                                    <input type="number" step="0.01" name="payrolls[{{ $emp->id }}][extra_half_days]"
                                        class="form-control " value="{{$payroll->extra_half_days}}">
                                    <input type="number" step="0.01"
                                        name="payrolls[{{ $emp->id }}][extra_half_days_amount]"
                                        class="form-control earning-input" readonly value="{{$payroll->extra_half_days_amount}}">
                                </div>
                            </td>

                            <td>
                                <div style="display: flex; gap: 5px;">
                                    <input type="number" step="0.01"
                                        name="payrolls[{{ $emp->id }}][poovarasan_kuda_allowance_150]"
                                        class="form-control " value="{{$payroll->poovarasan_kuda_allowance_150}}">
                                    <input type="number" step="0.01"
                                        name="payrolls[{{ $emp->id }}][poovarasan_kuda_allowance_150_amount]"
                                        class="form-control earning-input" readonly value="{{$payroll->poovarasan_kuda_allowance_150_amount}}">
                                </div>

                            </td>

                            <td><input type="number" step="0.01" name="payrolls[{{ $emp->id }}][labour_amount]"
                                    class="form-control earning-input" value="{{$payroll->labour_amount}}"></td>

                            {{-- Totals --}}
                            <td class="text-end ded-cell">{{ number_format($payroll->total_deductions, 2) }}</td>
                            <td class="text-end net-cell fw-semibold">{{ number_format($payroll->net_pay, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th class="text-end total-basic">0.00</th>

                            {{-- Earnings totals --}}
                            @foreach($earningComponents as $ec)
                            <th class="text-end total-earning" data-component-id="{{ $ec->id }}">0.00</th>
                            @endforeach

                            <th class="text-end total-overtime-hours">0.00</th>
                            <th class="text-end total-overtime-amount">0.00</th>
                            <th class="text-end total-gross">0.00</th>

                            {{-- Deductions totals --}}
                            @foreach($deductionComponents as $dc)
                            <th class="text-end total-deduction" data-component-id="{{ $dc->id }}">0.00</th>
                            @endforeach
                            <th class="text-end">0.00</th>
                            <th class="text-end total-no_pay">0.00</th>
                            <th class="text-end">0.00</th>
                            <th class="text-end">0.00</th>
                            <th class="text-end">0.00</th>
                            <th class="text-end">0.00</th>
                            <th class="text-end">0.00</th>
                            <th class="text-end total-deductions">0.00</th>
                            <th class="text-end total-net">0.00</th>
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
                                    <td>Basic Salary</td>
                                    <td class="text-end summary-basic">0.00</td>
                                </tr>
                                @foreach($earningComponents as $ec)
                                <tr>
                                    <td>{{ $ec->name }}</td>
                                    <td class="text-end summary-earning" data-component-id="{{ $ec->id }}">0.00</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td>Overtime Amount</td>
                                    <td class="text-end summary-overtime-amount">0.00</td>
                                </tr>
                                <tr>
                                    <td>Extra Amount</td>
                                    <td class="text-end summary-extra-amount">0.00</td>
                                </tr>
                                <tr class="fw-semibold">
                                    <td>Total Earnings</td>
                                    <td class="text-end summary-total-earnings">0.00</td>
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
                                    <td class="text-end summary-deduction" data-component-id="{{ $dc->id }}">0.00</td>
                                </tr>
                                @endif
                                @endforeach
                                <tr class="fw-semibold">
                                    <td>Total Deductions</td>
                                    <td class="text-end summary-total-deductions">0.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Right side: Employer Contributions (EPF 12% + ETF 3%) -->
                    <div class="col-md-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th colspan="2">Employer Contributions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>EPF (12%)</td>
                                    <td class="text-end summary-epf12">0.00</td>
                                </tr>
                                <tr>
                                    <td>ETF (3%)</td>
                                    <td class="text-end summary-etf3">0.00</td>
                                </tr>
                                <tr class="fw-semibold">
                                    <td>Total Employer</td>
                                    <td class="text-end summary-total-employer">0.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </form>

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
        let totalETF3 = 0;

        const earningTotals = {};
        const deductionTotals = {};

        table.querySelectorAll('tbody tr').forEach(tr => {
            const basic = parseFloat(tr.querySelector('input[name*="[basic_salary]"]').value || 0);

            const oneDaySalary = basic / 30;

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

            const noPayInput = tr.querySelector('input[name*="[no_pay]"]');
            let noPay = parseFloat(noPayInput?.value || 0);

            const adjustedBase = basic - noPay;

            const overtimeHours = parseFloat(tr.querySelector('input[name*="[overtime_hours]"]')
                .value || 0);
            const overtimeAmount = parseFloat(tr.querySelector('input[name*="[overtime_amount]"]')
                .value || 0);

            let gross = adjustedBase + overtimeAmount;
            let ded = 0;

            // Earnings
            tr.querySelectorAll('.earning-input').forEach(input => {
                const val = parseFloat(input.value || 0);
                const compId = input.name.match(/\[(\d+)\]$/)?. [1];
                if (compId) {
                    earningTotals[compId] = (earningTotals[compId] || 0) + val;
                }
                gross += val;
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
            tr.querySelector('.net-cell').textContent = (gross - ded).toFixed(2);

            totalBasic += adjustedBase;
            totalOvertimeHours += overtimeHours;
            totalOvertimeAmount += overtimeAmount;
            totalGross += gross;
            totalDeductions += ded;
            totalNet += (gross - ded);

            totalEPF12 += adjustedBase * 0.12;
            totalETF3 += adjustedBase * 0.03;
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

        updateFooterSummary(totalEPF12, totalETF3);
    }

    function updateFooterSummary(totalEPF12, totalETF3) {
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
        document.querySelector('.summary-total-deductions').textContent = totalDeds.toFixed(2);

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