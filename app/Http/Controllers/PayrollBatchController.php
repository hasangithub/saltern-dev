<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\JournalDetail;
use App\Models\JournalEntry;
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
use Carbon\Carbon;

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
            return redirect()->route('payroll.batches.index')->with('error', 'Only draft payroll batches can be edited.');
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

    public function print(Request $request, $id)
    {
        $department = $request->get('department', 'all');
        $batch = PayrollBatch::with([
            'payrolls' => function ($query) use ($department) {
                $query->with(['deductions', 'earnings', 'employee.user', 'employee.staffLoans']);

                if ($department && $department !== 'all') {
                    $query->whereHas('employee', function ($q) use ($department) {
                        $q->where('department', $department);
                    });
                }
            }
        ])->findOrFail($id);

        // Load all employees who should have payroll in this batch
        $employees = $batch->payrolls->load('employee.user', 'employee.staffLoans');

        // Load earning and deduction components
        $earningComponents = PayrollComponent::where('type', 'earning')->get();
        $deductionComponents = PayrollComponent::where('type', 'deduction')->get();

        $pdf = Pdf::loadView('payroll.print_payroll', compact('batch', 'earningComponents', 'deductionComponents', 'employees', 'department'))->setPaper('legal', 'landscape');

        // Optionally, force download
        return $pdf->stream("Payroll_{$batch->pay_period}.pdf");
    }

    public function store(Request $request)
    {
        // Validate pay period in "YYYY-MM"
        $request->validate([
            'pay_period' => ['required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/', 'unique:payroll_batches,pay_period'],
        ], [
            'pay_period.unique' => 'A payroll batch for this month already exists.',
            'pay_period.regex' => 'Pay period must be in format YYYY-MM.',
        ]);

        $employees = Employee::where('employment_status', 'active')->get();

        $batch = PayrollBatch::create([
            'pay_period' => $request->pay_period,
            'status' => 'draft',
            'processed_by' => auth('web')->id(),
        ]);

        foreach ($employees as $employee) {
            Payroll::create([
                'batch_id'      => $batch->id,
                'employee_id'   => $employee->id,
                'basic_salary'  => $employee->base_salary,
            ]);
        }

        return redirect()->route('payroll.batches.edit', $batch->id);
    }

    public function build(PayrollBatch $batch)
    {
        abort_if($batch->status !== 'draft', 403, 'Only draft batches can be edited.');

        $employees = Employee::with('user', 'staffLoans.staffLoanRepayment')->where('employment_status', 'active')
            ->orderBy('id')
            ->get();

        $earningComponents = PayrollComponent::where('type', 'earning')->orderBy('is_fixed', 'desc')->orderBy('name')->get();
        $deductionComponents = PayrollComponent::where('type', 'deduction')->orderBy('is_fixed', 'desc')->orderBy('name')->get();

        return view('payroll.build', compact('batch', 'employees', 'earningComponents', 'deductionComponents'));
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
                $oneDaySalary = $employee->base_salary / 30;

                $overtimeHours = (float)($payrollsInput[$employeeId]['overtime_hours'] ?? 0);
                $overtimeAmount = ($effectiveSalary / 30) * (3 / 16) * $overtimeHours;


                // Extra day calculations
                $mercantileDays = (float)($payrollsInput[$employeeId]['mercantile_days'] ?? 0);
                $mercantileAmount = ($oneDaySalary) * $mercantileDays;

                $extraFullDays = (float)($payrollsInput[$employeeId]['extra_full_days'] ?? 0);
                $extraFullAmount = ($oneDaySalary) * $extraFullDays;

                $extraHalfDays = (float)($payrollsInput[$employeeId]['extra_half_days'] ?? 0);
                $extraHalfAmount = ($oneDaySalary / 2) * $extraHalfDays;

                $poovarasanDays = (float)($payrollsInput[$employeeId]['poovarasan_kuda_allowance_150'] ?? 0);
                $poovarasanAmount = $poovarasanDays * 150;

                $labour_hours = (float)($payrollsInput[$employeeId]['labour_hours'] ?? 0);
                $labourAmount = (float)($payrollsInput[$employeeId]['labour_amount'] ?? 0);

                $extraWork = $mercantileAmount + $extraFullAmount + $extraHalfAmount + $poovarasanAmount + $labourAmount;

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

                $gross = $effectiveSalary + $overtimeAmount;
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
                    'net_pay' => $extraWork + $gross - $ded,
                    'epf_employee' => $epfEmployee,
                    'epf_employer' => $epfEmployer,
                    'etf' => $etf,
                    'overtime_hours' => $overtimeHours,
                    'overtime_amount' => $overtimeAmount,
                    'mercantile_days' => $mercantileDays,
                    'mercantile_days_amount' => $mercantileAmount,
                    'extra_full_days' => $extraFullDays,
                    'extra_full_days_amount' => $extraFullAmount,
                    'extra_half_days' => $extraHalfDays,
                    'extra_half_days_amount' => $extraHalfAmount,
                    'poovarasan_kuda_allowance_150' => $poovarasanDays,
                    'poovarasan_kuda_allowance_150_amount' => $poovarasanAmount,
                    'labour_hours' => $labour_hours,
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
                $oneDaySalary = $master->basic_salary / 30;

                $overtimeHours = (float)($payrollsInput[$employeeId]['overtime_hours'] ?? 0);
                $overtimeAmount = ($effectiveSalary / 30) * (3 / 16) * $overtimeHours;

                // Extra day calculations
                $mercantileDays = (float)($payrollsInput[$employeeId]['mercantile_days'] ?? 0);
                $mercantileAmount = ($oneDaySalary) * $mercantileDays;

                $extraFullDays = (float)($payrollsInput[$employeeId]['extra_full_days'] ?? 0);
                $extraFullAmount = ($oneDaySalary) * $extraFullDays;

                $extraHalfDays = (float)($payrollsInput[$employeeId]['extra_half_days'] ?? 0);
                $extraHalfAmount = ($oneDaySalary / 2) * $extraHalfDays;

                $poovarasanDays = (float)($payrollsInput[$employeeId]['poovarasan_kuda_allowance_150'] ?? 0);
                $poovarasanAmount = $poovarasanDays * 150;

                $labour_hours = (float)($payrollsInput[$employeeId]['labour_hours'] ?? 0);
                $labourAmount = (float)($payrollsInput[$employeeId]['labour_amount'] ?? 0);

                $extraWork = $mercantileAmount + $extraFullAmount + $extraHalfAmount + $poovarasanAmount + $labourAmount;




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
                $gross = $effectiveSalary + $overtimeAmount;
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

                // EPF/ETF Calculations
                $epfEmployee = $effectiveSalary * 0.08;
                $epfEmployer = $effectiveSalary * 0.12;
                $etf = $effectiveSalary * 0.03;

                $ded += $epfEmployee; // only EPF 8% counts in deductions for net pay

                // Update payroll totals
                $master->update([
                    'gross_earnings' => $gross,
                    'total_deductions' => $ded,
                    'net_pay' => $extraWork + $gross - $ded,
                    'epf_employee' => $epfEmployee,
                    'epf_employer' => $epfEmployer,
                    'etf' => $etf,
                    'overtime_hours' => $overtimeHours,
                    'overtime_amount' => $overtimeAmount,
                    'mercantile_days' => $mercantileDays,
                    'mercantile_days_amount' => $mercantileAmount,
                    'extra_full_days' => $extraFullDays,
                    'extra_full_days_amount' => $extraFullAmount,
                    'extra_half_days' => $extraHalfDays,
                    'extra_half_days_amount' => $extraHalfAmount,
                    'poovarasan_kuda_allowance_150' => $poovarasanDays,
                    'poovarasan_kuda_allowance_150_amount' => $poovarasanAmount,
                    'labour_hours' => $labour_hours,
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

    public function show(Request $request, $id)
    {
        $department = $request->get('department', 'all'); // default = all

        $batch = PayrollBatch::with([
            'payrolls' => function ($query) use ($department) {
                $query->with(['deductions', 'earnings', 'employee.user', 'employee.staffLoans']);

                if ($department && $department !== 'all') {
                    $query->whereHas('employee', function ($q) use ($department) {
                        $q->where('department', $department);
                    });
                }
            }
        ])->findOrFail($id);



        // Load all employees who should have payroll in this batch
        $employees = $batch->payrolls->load('employee.user', 'employee.staffLoans');

        // Load earning and deduction components
        $earningComponents = PayrollComponent::where('type', 'earning')->get();
        $deductionComponents = PayrollComponent::where('type', 'deduction')->get();

        return view('payroll.show', compact('batch', 'earningComponents', 'deductionComponents', 'employees', 'department'));
    }

    public function approve($id)
    {
        $batch = PayrollBatch::findOrFail($id);

        if ($batch->status !== 'draft') {
            return redirect()->back()->with('error', 'This payroll batch is already approved.');
        }

        $batch->status = 'approved';
        $batch->save();

        // Calculate totals
    $totalNetPay = $batch->payrolls->sum('net_pay');
    $totalEpf8 = $batch->payrolls->sum('epf_employee');
    
    $totalDeductions = [];
    foreach ($batch->payrolls as $payroll) {
        foreach ($payroll->deductions as $deduction) {
            $name = strtolower($deduction->component->name);
            $totalDeductions[$name] = ($totalDeductions[$name] ?? 0) + $deduction->amount;
        }
    }

    // Create journal entry
    $journal = JournalEntry::create([
        'journal_date' => Carbon::now()->toDateString(),
        'description' => 'Payroll Batch #' . $batch->id,
    ]);

    $details = [];

    // === Debit Entries (All go to Ledger 102) ===
    $details[] = [
        'journal_id' => $journal->id,
        'ledger_id' => 102,
        'sub_ledger_id' => null,
        'debit_amount' => $totalNetPay,
        'credit_amount' => null,
        'description' => 'Net Pay for Payroll Batch #' . $batch->id,
    ];

    $details[] = [
        'journal_id' => $journal->id,
        'ledger_id' => 102,
        'sub_ledger_id' => null,
        'debit_amount' => $totalEpf8,
        'credit_amount' => null,
        'description' => 'EPF8 for Payroll Batch #' . $batch->id,
    ];

    foreach (['salary advance','festival loan','loan','union','fine'] as $key) {
        if (!empty($totalDeductions[$key])) {
            $details[] = [
                'journal_id' => $journal->id,
                'ledger_id' => 102,
                'sub_ledger_id' => null,
                'debit_amount' => $totalDeductions[$key],
                'credit_amount' => null,
                'description' => ucfirst(str_replace('_',' ',$key)) . " for Payroll Batch #" . $batch->id,
            ];
        }
    }

    // === Credit Entries (Batch-level totals) ===
    $creditMap = [
        'net_pay' => 177,
        'salary advance' => 178,
        'union' => 107,
        'fine' => 111,
        'epf_8' => 103,
        'loan' => ['ledger_id'=>12, 'sub_ledger_id'=>116],
        'festival loan' => ['ledger_id'=>12, 'sub_ledger_id'=>116],
    ];

    // Net Pay
    $details[] = [
        'journal_id' => $journal->id,
        'ledger_id' => $creditMap['net_pay'],
        'sub_ledger_id' => null,
        'debit_amount' => null,
        'credit_amount' => $totalNetPay,
        'description' => 'Net Pay for Payroll Batch #' . $batch->id,
    ];

    $details[] = [
        'journal_id' => $journal->id,
        'ledger_id' => 103,
        'sub_ledger_id' => null,
        'debit_amount' => null,
        'credit_amount' => $totalEpf8,
        'description' => 'EPF8 for Payroll Batch #' . $batch->id,
    ];

    // Other deductions
    foreach (['salary advance','festival loan','loan','union','fine'] as $key) {
        if (!empty($totalDeductions[$key])) {
            $ledgerId = is_array($creditMap[$key]) ? $creditMap[$key]['ledger_id'] : $creditMap[$key];
            $subLedgerId = is_array($creditMap[$key]) ? $creditMap[$key]['sub_ledger_id'] : null;

            $details[] = [
                'journal_id' => $journal->id,
                'ledger_id' => $ledgerId,
                'sub_ledger_id' => $subLedgerId,
                'debit_amount' => null,
                'credit_amount' => $totalDeductions[$key],
                'description' => ucfirst(str_replace('_',' ',$key)) . " for Payroll Batch #" . $batch->id,
            ];
        }
    }

    // Bulk insert
    JournalDetail::insert($details);

        return redirect()->route('payroll.batches.index')->with('success', 'Payroll batch approved successfully.');
    }
}
