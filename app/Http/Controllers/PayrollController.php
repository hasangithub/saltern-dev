<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Payroll;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function generateCurrentMonth()
{
    $month = Carbon::now()->startOfMonth(); // e.g., 2025-05-01

    foreach (Employee::all() as $employee) {
        $this->generateEmployeePayroll($employee, $month);
    }

    return redirect()->back()->with('success', 'Payroll generated for all employees for ' . $month->format('F Y'));
}

    public function index()
    {
        $years = range(date('Y'), date('Y') - 10);
        $months = range(1, 12);
        return view('payroll.index', compact('years', 'months'));
    }

    public function view(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $payrolls = Payroll::with('user')
        ->whereYear('month', $year)
        ->whereMonth('month', $month)
            ->get();

        return view('payroll.view', compact('payrolls'));
    }

    private function generateEmployeePayroll($employee, $month)
{
    $attendances = Attendance::where('user_id', $employee->user_id)
        ->whereYear('attendance_date', $month->year)
        ->whereMonth('attendance_date', $month->month)
        ->get();

    $present = $attendances->where('status', 'present')->count();
    $leave = $attendances->where('status', 'leave')->count();
    $absent = $attendances->where('status', 'absent')->count();

    $basicSalary = $employee->base_salary;
    $deductions = $absent * 500; // example value
    $netSalary = $basicSalary - $deductions;

    Payroll::updateOrCreate(
        [
            'user_id' => $employee->user_id,
            'month' => $month->format('Y-m-d'),
        ],
        [
            'total_days'   => 30,
            'present_days' => $present,
            'leave_days'   => $leave,
            'half_days'    => $leave,
            'no_pay_days'   => $leave,
            'basic_salary' => $basicSalary,
            'deductions' => $deductions,
            'net_salary' => $netSalary,
            'generated_at' => now(),
        ]
    );
}

}