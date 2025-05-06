<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

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
        $data = $request->validate([
            'person_id' => 'required|string|unique:employees,person_id',
            'full_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'designation' => 'required|string',
            'base_salary' => 'required|numeric',
            'join_date' => 'required|date',
            'employment_status' => 'required|in:Active,Inactive,Resigned,Terminated',
        ]);

        $user = User::create([
            'name' => $data['full_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Create employee data and link to user
        $employee = Employee::create([
            'user_id' => $user->id,
            'person_id' => $data['person_id'],
            'designation' => $data['designation'],
            'base_salary' => $data['base_salary'],
            'join_date' => $data['join_date'],
            'employment_status' => $data['employment_status'],
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