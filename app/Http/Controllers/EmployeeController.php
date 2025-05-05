<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('user')->get(); // eager load user
        return view('employees.index', compact('employees'));
    }
    
    public function create()
    {
        $users = User::all();
    return view('employees.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'designation' => 'required|string',
            'base_salary' => 'required|numeric',
            'join_date' => 'required|date',
            'employment_status' => 'required|in:Active,Inactive,Resigned,Terminated',
        ]);

        // Create employee data and link to user
        $employee = Employee::create([
            'user_id' => $request->user_id,
            'designation' => $request->designation,
            'base_salary' => $request->base_salary,
            'join_date' => $request->join_date,
            'employment_status' => $request->employment_status,
        ]);

        // Optionally, you can update the user table with employee info:
        // $user = User::findOrFail($request->user_id);
        // $user->designation = $request->designation;
        // $user->base_salary = $request->base_salary;
        // $user->join_date = $request->join_date;
        // $user->employment_status = $request->employment_status;
        // $user->save();

        return redirect()->route('employees.index')->with('success', 'Employee registered successfully!');
    }
}
