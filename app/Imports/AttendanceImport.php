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
        return 6; // Start reading from row 6 (after the header on row 5)
    }

    public function collection(Collection $rows)
    {
        $presentPersonIds = [];
        $attendanceDate = null;

        foreach ($rows as $row) {

            $personId = $row[1] ?? null; // Column B
            $dateRaw = $row[6] ?? null;  // Column G
            $punchesRaw = trim($row[9] ?? '');
            $personId = (int) $personId;


            if (!$personId || !$dateRaw) {
                continue;
            }

            if (is_numeric($dateRaw)) {
                $date = \Carbon\Carbon::instance(ExcelDate::excelToDateTimeObject($dateRaw));
            } else {
                $date = \Carbon\Carbon::parse($dateRaw);
            }


            try {
                $date = $date->format('Y-m-d');
            } catch (\Exception $e) {
                continue; // Skip invalid date rows
            }

            
            $presentPersonIds[] = $personId;

            $employee = Employee::where('person_id', $personId)->first();

            if (!$employee) continue;

            // if it's "-" or empty → no punch → absent
            if (empty($punchesRaw) || trim($punchesRaw) === '-') {
                $punchTimes = [];
                $workedHours = 0;
                $status = 0; // absent
            } else {
                // split punches into array
                $punchTimes = preg_split('/\s+/', trim($punchesRaw));

                // calculate hours (pair IN/OUT)
                $workedHours = 0;
                for ($i = 0; $i < count($punchTimes); $i += 2) {
                    if (isset($punchTimes[$i + 1])) {
                        $in  = Carbon::createFromFormat('H:i:s', $punchTimes[$i]);
                        $out = Carbon::createFromFormat('H:i:s', $punchTimes[$i + 1]);
                        $workedHours += $in->diffInMinutes($out) / 60;
                    }
                }

                $status = 1; // present
            }

            // Save into DB
            Attendance::updateOrCreate(
                [
                    'user_id' => $employee->user_id,
                    'attendance_date' => $date,
                ],
                [
                    'status' => $status,
                    'punch_times' => json_encode($punchTimes),
                    'worked_hours' => round($workedHours, 2),
                ]
            );
        }
    }
}
