<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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
            'full_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'designation' => 'required|string',
            'base_salary' => 'required|numeric',
            'join_date' => 'required|date',
            'employment_status' => 'required|in:Active,Inactive,Resigned,Terminated',
        ]);

        DB::beginTransaction();

        try {
            // Create user
            $user = User::create([
                'name' => $data['full_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            // Create employee linked to the user
            Employee::create([
                'user_id' => $user->id,
                'designation' => $data['designation'],
                'base_salary' => $data['base_salary'],
                'join_date' => $data['join_date'],
                'employment_status' => $data['employment_status'],
            ]);

            DB::commit();

            return redirect()->route('employees.index')->with('success', 'Employee registered successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors(['error' => 'Registration failed. Please try again.']);
        }
    }
}