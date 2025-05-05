<?php 

namespace App\Imports;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;


class AttendanceImport implements ToCollection, WithStartRow
{
    public function startRow(): int
    {
        return 2; // Start reading from row 6 (after the header on row 5)
    }

    public function collection(Collection $rows)
    {
        $presentPersonIds = [];
        $attendanceDate = null;

        foreach ($rows as $row) {
           
            $personId = $row[1] ?? null; // Column B
            $dateRaw = $row[6] ?? null;  // Column G
            $personId = (int) $personId;

            if (is_numeric($dateRaw)) {
                $date = \Carbon\Carbon::instance(ExcelDate::excelToDateTimeObject($dateRaw));
            } else {
                $date = \Carbon\Carbon::parse($dateRaw);
            }

          
          
            if (!$personId || !$dateRaw) {
                continue;
            }

            try {
                $date = $date->format('Y-m-d');
            } catch (\Exception $e) {
                continue; // Skip invalid date rows
            }

            $attendanceDate = $attendanceDate ?? $date;
            $presentPersonIds[] = $personId;

            $employee = Employee::where('person_id', $personId)->first();

            if ($employee) {
                Attendance::updateOrCreate(
                    [
                        'user_id' => $employee->user_id,
                        'attendance_date' => $date,
                    ],
                    [
                        'status' => 'present',
                    ]
                );
            }
        }

        // Now mark absents for that date
        if ($attendanceDate) {
            $allEmployees = Employee::all();

            foreach ($allEmployees as $employee) {
                if (!in_array($employee->person_id, $presentPersonIds)) {
                    Attendance::updateOrCreate(
                        [
                            'user_id' => $employee->user_id,
                            'attendance_date' => $attendanceDate,
                        ],
                        [
                            'status' => 'absent',
                        ]
                    );
                }
            }
        }
    }
}
