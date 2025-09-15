<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\AttendanceImport;
use App\Models\Attendance;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('user');

        // Optional filter by date
        if ($request->has('date') && $request->date) {
            $query->where('attendance_date', $request->date);
        }

        $attendances = $query->orderBy('attendance_date', 'desc')->get();

        return view('attendance.index', compact('attendances'));
    }

    public function importForm()
    {
        return view('attendance.import');
    }

    public function import(Request $request)
    {  
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        Excel::import(new AttendanceImport, $request->file('file'));

        return redirect()->back()->with('success', 'Attendance imported successfully!');
    }
}
