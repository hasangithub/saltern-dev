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
            <h4 class="mb-0"> Payroll — {{ $batch->pay_period }}</h4>
            <small class="text-muted">Status: {{ ucfirst($batch->status) }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('payroll.batches.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>
    <div>
        <form method="GET" action="{{ route('payroll.batches.contractShow', $batch->id) }}" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <select name="department" class="form-control" onchange="this.form.submit()">
                        <option value="all" {{ $department == 'all' ? 'selected' : '' }}>All Departments</option>
                        <option value="office" {{ $department == 'office' ? 'selected' : '' }}>Office</option>
                        <option value="workshop" {{ $department == 'workshop' ? 'selected' : '' }}>Workshop</option>
                        <option value="security" {{ $department == 'security' ? 'selected' : '' }}>Security</option>
                    </select>
                </div>
            </div>
        </form>

        {{-- Print Button --}}
        <a href="{{ route('payroll.batches.contractPrint', ['batch' => $batch->id, 'department' => $department]) }}"
            target="_blank" class="btn btn-secondary">
            <i class="fas fa-print"></i> Print
        </a>
        <a href="{{ route('payroll.batches.contractPayslips', $batch->id) }}" class="btn btn-sm btn-success"
            target="_blank">
            <i class="bi bi-printer"></i> Print Payslips
        </a>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
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
       <div class="table-responsive">
       <table class=" table table-main small">
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
                    <th style="width:40px;">8 Hours Duty</th>
                    <th style="width:40px;">12 Hours Duty</th>
                    <th style="width:40px;">Poovarsan kuda 150 Payments</th>
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
                    <td class="text-right">{{ number_format($payroll->eight_hours_duty_amount ?? 0,2) }}</td>
                   
                    <td class="text-right">{{ number_format($payroll->extra_half_days_amount ?? 0,2) }}</td>
                    <td class="text-right">{{ number_format($payroll->poovarasan_kuda_allowance_150_amount ?? 0,2) }}
                    </td>
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
                <td style="width:40px;">{{ number_format($batch->payrolls->sum('day_salary'),2) }}</td>

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
                <td style="width:40px;" class="text-right">
                    {{ number_format($batch->payrolls->sum('eight_hours_duty_amount'),2) }}</td>
                <td style="width:40px;" class="text-right">
                    {{ number_format($batch->payrolls->sum('extra_half_days_amount'),2) }}</td>
                <td style="width:40px;" class="text-right">
                    {{ number_format($batch->payrolls->sum('poovarasan_kuda_allowance_150_amount'),2) }}</td>
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
       </div>

        {{-- === Summary Tables (side-by-side using floats) === --}}
        <div class="summary-wrapper">
            {{-- Earnings Summary --}}
            <table class=" table summary-table small">
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
            <table class=" table summary-table small" style="line-height: 13px;">
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
            <table class=" table summary-table small" style="line-height: 18.5px;">
                <thead>
                    <tr>
                        <th colspan="2">Summary</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Earnings</td>
                        <td class="text-right">{{ number_format($batch->payrolls->sum(fn($p) => $p->eight_hours_duty_amount  + $p->extra_half_days_amount + $p->poovarasan_kuda_allowance_150_amount + $p->labour_amount) + $batch->payrolls->sum('gross_earnings'),2) }}</td>
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

</div>
@stop

{{-- Push extra CSS --}}

@push('css')
{{-- Add here extra stylesheets --}}
{{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@endpush

{{-- Push extra scripts --}}

@push('js')

@endpush