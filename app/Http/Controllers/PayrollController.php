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
    $month = Carbon::now()->subMonth()->startOfMonth(); // Returns 2025-04-01

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

            $totals = [
                'basic_salary'   => $payrolls->sum('basic_salary'),
                'epf_employee'   => $payrolls->sum('epf_employee'),
                'epf_employer'   => $payrolls->sum('epf_employer'),
                'etf'            => $payrolls->sum('etf'),
                'net_salary'     => $payrolls->sum('net_salary'),
            ];

        return view('payroll.view', compact('payrolls', 'totals'));
    }

    private function generateEmployeePayroll($employee, $month)
{
    $attendances = Attendance::where('user_id', $employee->user_id)
        ->whereYear('attendance_date', $month->year)
        ->whereMonth('attendance_date', $month->month)
        ->get();

    $present = $attendances->where('status', 'present')->count();
    $halfDay = $attendances->where('status', 'half-day')->count();
    $absent = $attendances->where('status', 'absent')->count();

    $basicSalary = $employee->base_salary;

    $yearLeaveUsed = Attendance::where('user_id', $employee->user_id)
        ->whereYear('attendance_date', $month->year)
        ->where('status', 'absent')
        ->count();

    // 4. Check if exceeded entitlement
    $extraLeave = max(0, $yearLeaveUsed - $employee->annual_leave_entitlement);

    // 5. Calculate total no-pay days
    $workingDays = 30; // Or calculate based on calendar
    $effectiveWorked = $present + $absent + ($halfDay * 0.5);

    // Add extra leaves to no-pay
    $noPayDays = max(0, $workingDays - $effectiveWorked + $extraLeave);

    // 6. No-pay deduction
    $dailyRate = $basicSalary / $workingDays;
    $noPayDeduction = round($dailyRate * $noPayDays, 2);

    $netSalary = $basicSalary - $noPayDeduction;

    $epfEmployee = round($basicSalary * 0.08, 2);  // 8% employee share
    $epfEmployer = round($basicSalary * 0.12, 2);  // 12% employer share
    $etf = round($basicSalary * 0.03, 2);          // 3% employer ETF

    $netSalary = $netSalary - $epfEmployee;

    Payroll::updateOrCreate(
        [
            'user_id' => $employee->user_id,
            'month' => $month->format('Y-m-d'),
        ],
        [
            'total_days'   => $workingDays,
            'present_days' => $present,
            'leave_days'   => $absent,
            'half_days'    => $halfDay,
            'no_pay_days'   => $noPayDays,
            'basic_salary' => $basicSalary,
            'deductions' => $noPayDeduction,
            'net_salary' => $netSalary,
            'epf_employee' => $epfEmployee,
            'epf_employer' => $epfEmployer,
            'etf' => $etf,
            'generated_at' => now(),
        ]
    );
}

}