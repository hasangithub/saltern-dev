<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeaveController extends Controller
{

    public function index()
    {
        $leaves = Leave::with('employee')->get(); // eager load user
        return view('leaves.index', compact('leaves'));
    }

    public function createRequest()
    {
        return view('leaves.request');
    }
    
    public function requestLeave(Request $request)
{
    $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'reason' => 'nullable|string'
    ]);

    $totalDays = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) + 1;

    Leave::create([
        'employee_id' => 1,//auth()->user()->id,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'total_days' => $totalDays,
        'reason' => $request->reason
    ]);

    return back()->with('success', 'Leave request submitted successfully.');
}

public function approveLeave($id)
{
    $leave = Leave::findOrFail($id);
    $leave->status = 'Approved';
    $leave->save();

    return back()->with('success', 'Leave approved.');
}

public function rejectLeave($id)
{
    $leave = Leave::findOrFail($id);
    $leave->status = 'Rejected';
    $leave->save();

    return back()->with('error', 'Leave rejected.');
}

}