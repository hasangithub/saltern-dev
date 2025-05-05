<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\AttendanceImport;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
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
