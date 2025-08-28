<?php 

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PayrollBatch;
use App\Models\PayrollComponent;
use App\Models\PayrollDeduction;
use App\Models\PayrollEarning;
use App\Models\Payroll;
use App\Models\StaffLoan;
use App\Models\StaffLoanRepayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollBatchController extends Controller
{
    public function index()
    {
        $batches = PayrollBatch::withCount('payrolls')
            ->orderByDesc('pay_period')
            ->get();

        return view('payroll.index', compact('batches'));
    }

    public function create()
    {
        return view('payroll.create');
    }

    public function edit($id)
    {
        $batch = PayrollBatch::with(['payrolls.deductions', 'payrolls.earnings'])->findOrFail($id);

        // Allow edit only if batch is still draft
        if ($batch->status !== 'draft') {
            return redirect()->route('payroll.index')->with('error', 'Only draft payroll batches can be edited.');
        }

            // Load all employees who should have payroll in this batch
        $employees = $batch->payrolls->load('employee.user', 'employee.staffLoans');

        // Load earning and deduction components
        $earningComponents = PayrollComponent::where('type', 'earning')->get();
        $deductionComponents = PayrollComponent::where('type', 'deduction')->get();

        // Pass to view
        return view('payroll.edit', compact(
            'batch',
            'employees',
            'earningComponents',
            'deductionComponents'
        ));
    }

    public function print($id)
    {
        $batch = PayrollBatch::with(['payrolls.deductions', 'payrolls.earnings'])->findOrFail($id);

        // Allow edit only if batch is still draft
        if ($batch->status !== 'draft') {
            return redirect()->route('payroll.index')->with('error', 'Only draft payroll batches can be edited.');
        }

            // Load all employees who should have payroll in this batch
        $employees = $batch->payrolls->load('employee.user', 'employee.staffLoans');

        // Load earning and deduction components
        $earningComponents = PayrollComponent::where('type', 'earning')->get();
        $deductionComponents = PayrollComponent::where('type', 'deduction')->get();

        $pdf = Pdf::loadView('payroll.print_payroll', compact('batch', 'earningComponents', 'deductionComponents', 'employees'))->setPaper('A4', 'landscape');
        
        // Optionally, force download
        return $pdf->stream("Payroll_{$batch->pay_period}.pdf");
    }

    public function store(Request $request)
    {
        // Validate pay period in "YYYY-MM"
        $request->validate([
            'pay_period' => ['required','regex:/^\d{4}-(0[1-9]|1[0-2])$/','unique:payroll_batches,pay_period'],
        ], [
            'pay_period.unique' => 'A payroll batch for this month already exists.',
            'pay_period.regex' => 'Pay period must be in format YYYY-MM.',
        ]);

        $batch = PayrollBatch::create([
            'pay_period' => $request->pay_period,
            'status' => 'draft',
            'processed_by' => auth('web')->id(),
        ]);

        return redirect()->route('payroll.batches.build', $batch);
    }

    public function build(PayrollBatch $batch)
    {
        abort_if($batch->status !== 'draft', 403, 'Only draft batches can be edited.');

        $employees = Employee::with('user', 'staffLoans.staffLoanRepayment')->where('employment_status','active')
            ->orderBy('id')
            ->get();

        $earningComponents = PayrollComponent::where('type','earning')->orderBy('is_fixed','desc')->orderBy('name')->get();
        $deductionComponents = PayrollComponent::where('type','deduction')->orderBy('is_fixed','desc')->orderBy('name')->get();

        return view('payroll.build', compact('batch','employees','earningComponents','deductionComponents'));
    }

    public function save(Request $request, PayrollBatch $batch)
    {   
        abort_if($batch->status !== 'draft', 403, 'Only draft batches can be saved.');

        // Expect arrays shaped as:
        // earnings[employee_id][component_id or "name:xyz"] = amount
        // deductions[employee_id][component_id or "name:xyz"] = amount
        $data = $request->validate([
            'earnings' => 'array',
            'deductions' => 'array',
            'loan' => 'array',
            'payrolls' => 'array', // for no_pay
        ]);

        DB::transaction(function () use ($batch, $data) {

            $loanIds = $data['loan'] ?? [];           // loan_id[employee_id] = selected loan
           
            $earnings = $data['earnings'] ?? [];
            $deductions = $data['deductions'] ?? [];
            $payrollsInput = $data['payrolls'] ?? [];

            $employeeIds = collect(array_unique(array_merge(array_keys($earnings), array_keys($deductions))))
                ->map(fn($id) => (int)$id)
                ->filter();

            foreach ($employeeIds as $employeeId) {

                $employee = Employee::find($employeeId);

                $noPay = floatval($payrollsInput[$employeeId]['no_pay'] ?? 0);
                $effectiveSalary = $employee->base_salary - $noPay;

                  // Extra day calculations
            $mercantileDays = (float)($payrollsInput[$employeeId]['mercantile_days'] ?? 0);
            $mercantileAmount = ($effectiveSalary / 30) * $mercantileDays;

            $extraFullDays = (float)($payrollsInput[$employeeId]['extra_full_days'] ?? 0);
            $extraFullAmount = ($effectiveSalary / 30) * $extraFullDays;

            $extraHalfDays = (float)($payrollsInput[$employeeId]['extra_half_days'] ?? 0);
            $extraHalfAmount = ($effectiveSalary / 30 / 2) * $extraHalfDays;

            $poovarasanDays = (float)($payrollsInput[$employeeId]['poovarasan_kuda_allowance_150'] ?? 0);
            $poovarasanAmount = $poovarasanDays * 150;

            $labourAmount = (float)($payrollsInput[$employeeId]['labour_amount'] ?? 0);

    
                // Upsert master
                $master = Payroll::firstOrCreate([
                    'batch_id'    => $batch->id,
                    'employee_id' => $employeeId,
                ], [
                    'basic_salary' => $employee->base_salary, // set base_salary on creation
                ]);

                $master->no_pay = $noPay;

                // Clear existing rows for idempotence (re-save cleanly)
                $master->earnings()->delete();
                $master->deductions()->delete();

                $gross = $effectiveSalary;
                $ded = 0;

                // Save earnings
                foreach (($earnings[$employeeId] ?? []) as $key => $amount) {
                    $amount = (float)($amount ?? 0);
                    if ($amount == 0) continue;

                    [$componentId, $componentName] = $this->resolveComponent($key, 'earning');
                    PayrollEarning::create([
                        'payroll_id' => $master->id,
                        'component_id' => $componentId,
                        'component_name' => $componentName,
                        'amount' => $amount,
                    ]);
                    $gross += $amount;
                }

                // Save deductions
                foreach (($deductions[$employeeId] ?? []) as $key => $amount) {
                    $amount = (float)($amount ?? 0);
                    if ($amount == 0) continue;

                    [$componentId, $componentName] = $this->resolveComponent($key, 'deduction');

                    $deductionData = [
                        'payroll_id'     => $master->id,
                        'component_id'   => $componentId,
                        'component_name' => $componentName,
                        'amount'         => $amount,
                    ];

                    $ded += $amount;

                    if (strtolower($componentName) === 'loan' && !empty($loanIds[$employeeId])) {

                        $deductionData['loan_id'] = $loanIds[$employeeId];
                        
                        StaffLoanRepayment::updateOrCreate(
                            [
                                'payroll_id' => $master->id,
                            ],
                            [
                                'staff_loan_id'     => $loanIds[$employeeId],
                                'amount' => $amount,
                                'repayment_date' => now(),
                                'status' => 'pending',
                            ]
                        );
                    }

                    PayrollDeduction::create($deductionData);
                }

                  // EPF/ETF Calculations
            $epfEmployee = $effectiveSalary * 0.08;
            $epfEmployer = $effectiveSalary * 0.12;
            $etf = $effectiveSalary * 0.03;

            $ded += $epfEmployee; // only EPF 8% counts in deductions for net pay

            $master->update([
                'gross_earnings' => $gross,
                'total_deductions' => $ded,
                'net_pay' => $gross - $ded,
                'epf_employee' => $epfEmployee,
                'epf_employer' => $epfEmployer,
                'etf' => $etf,
                'mercantile_days_amount' => $mercantileAmount,
                'extra_full_days_amount' => $extraFullAmount,
                'extra_half_days_amount' => $extraHalfAmount,
                'poovarasan_kuda_allowance_150_amount' => $poovarasanAmount,
                'labour_amount' => $labourAmount,
            ]);
            }
        });

        return redirect()
        ->route('payroll.batches.edit', $batch)
        ->with('success', 'Payroll updated successfully.');
    }

    public function update(Request $request, PayrollBatch $batch)
    {
        abort_if($batch->status !== 'draft', 403, 'Only draft batches can be updated.');

        $data = $request->validate([
            'earnings' => 'array',
            'deductions' => 'array',
            'loan' => 'array',
            'payrolls' => 'array', // for basic_salary, overtime, etc.
        ]);

        DB::transaction(function () use ($batch, $data) {

            $loanIds = $data['loan'] ?? [];
            $earnings = $data['earnings'] ?? [];
            $deductions = $data['deductions'] ?? [];
            $payrollsInput = $data['payrolls'] ?? [];

            $employeeIds = collect(array_unique(array_merge(array_keys($earnings), array_keys($deductions), array_keys($payrollsInput))))
                ->map(fn($id) => (int)$id)
                ->filter();

            foreach ($employeeIds as $employeeId) {

                $master = Payroll::where('batch_id', $batch->id)
                ->where('employee_id', $employeeId)
                ->first();

                if (!$master) {
                continue; // skip if payroll not exist
                }

                $noPay = floatval($payrollsInput[$employeeId]['no_pay'] ?? 0);
                $effectiveSalary = $master->basic_salary - $noPay;

                  // Extra day calculations
            $mercantileDays = (float)($payrollsInput[$employeeId]['mercantile_days'] ?? 0);
            $mercantileAmount = ($effectiveSalary / 30) * $mercantileDays;
            
            $extraFullDays = (float)($payrollsInput[$employeeId]['extra_full_days'] ?? 0);
            $extraFullAmount = ($effectiveSalary / 30) * $extraFullDays;

            $extraHalfDays = (float)($payrollsInput[$employeeId]['extra_half_days'] ?? 0);
            $extraHalfAmount = ($effectiveSalary / 30 / 2) * $extraHalfDays;

            $poovarasanDays = (float)($payrollsInput[$employeeId]['poovarasan_kuda_allowance_150'] ?? 0);
            $poovarasanAmount = $poovarasanDays * 150;

            $labourAmount = (float)($payrollsInput[$employeeId]['labour_amount'] ?? 0);

    

                // clear old earnings/deductions
                $master->earnings()->delete();
                $master->deductions()->delete();

                // update basic_salary, overtime, no_pay
                $master->update([
                'basic_salary' => $payrollsInput[$employeeId]['basic_salary'] ?? 0,
                'overtime_hours' => $payrollsInput[$employeeId]['overtime_hours'] ?? 0,
                'overtime_amount' => $payrollsInput[$employeeId]['overtime_amount'] ?? 0,
                'no_pay' => $payrollsInput[$employeeId]['no_pay'] ?? 0,
                ]);
                $gross = 0;
                $ded = 0;

                // --- Earnings ---
                foreach (($earnings[$employeeId] ?? []) as $key => $amount) {
                    $amount = (float)($amount ?? 0);
                    if ($amount == 0) continue;

                    [$componentId, $componentName] = $this->resolveComponent($key, 'earning');

                    PayrollEarning::create([
                        'payroll_id' => $master->id,
                        'component_id' => $componentId,
                        'component_name' => $componentName,
                        'amount' => $amount,
                    ]);

                    $gross += $amount;
                }

                // Add overtime_amount to gross
                $gross += $master->overtime_amount;

                // --- Deductions ---
                foreach (($deductions[$employeeId] ?? []) as $key => $amount) {
                    $amount = (float)($amount ?? 0);
                    if ($amount == 0) continue;

                    [$componentId, $componentName] = $this->resolveComponent($key, 'deduction');

                    $deductionData = [
                        'payroll_id' => $master->id,
                        'component_id' => $componentId,
                        'component_name' => $componentName,
                        'amount' => $amount,
                    ];

                    $ded += $amount;

                    // Handle loan
                    if (strtolower($componentName) === 'loan' && !empty($loanIds[$employeeId])) {
                        $deductionData['loan_id'] = $loanIds[$employeeId];

                        StaffLoanRepayment::updateOrCreate(
                            [
                                'payroll_id' => $master->id,
                            ],
                            [
                                'staff_loan_id' => $loanIds[$employeeId],
                                'amount' => $amount,
                                'repayment_date' => now(),
                                'status' => 'pending',
                            ]
                        );
                    }

                    PayrollDeduction::create($deductionData);
                }

                // Update payroll totals
                $master->update([
                    'gross_earnings' => $gross,
                    'total_deductions' => $ded,
                    'net_pay' => $gross - $ded,
                    'mercantile_days_amount' => $mercantileAmount,
                    'extra_full_days_amount' => $extraFullAmount,
                    'extra_half_days_amount' => $extraHalfAmount,
                    'poovarasan_kuda_allowance_150_amount' => $poovarasanAmount,
                    'labour_amount' => $labourAmount,
                ]);
            }
        });

        return redirect()
            ->route('payroll.batches.edit', $batch)
            ->with('success', 'Payroll updated successfully.');
    }


    /**
     * Accepts a key like "12" (component id) or "name:Custom Bonus"
     * Returns [component_id|null, component_name]
     */
    protected function resolveComponent(string $key, string $type): array
    {
        if (str_starts_with($key, 'name:')) {
            $name = trim(substr($key, 5));
            return [null, $name];
        }
        $component = PayrollComponent::where('id', (int)$key)->where('type', $type)->first();
        return [$component?->id, $component?->name ?? 'Unknown'];
    }
}
