<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('user.roles')->get(); // eager load user
        return view('employees.index', compact('employees'));
    }

     public function edit($id)
    {
        $user = auth('web')->user();
   
        if (!$user->hasRole('admin')) {
            abort(403);
        } 

        $employee = User::findOrFail($id); 
        $roles = Role::where('guard_name', 'web')->get();
        return view('employees.edit', compact('employee', 'roles'));
    }

    public function update(Request $request, $id)
{

    $user = User::with('employee')->findOrFail($id);

    $request->validate([
        'full_name' => 'required|string|max:255',
        'sort_order' => 'required|numeric',
        'email' => 'required|email|unique:users,email,' . $id,
        'designation' => 'required|string',
        'base_salary' => 'required|numeric',
        'epf_number' => 'nullable|string',
        'join_date' => 'required|date',
        'employment_status' => 'required|in:Active,Inactive,Resigned,Terminated',
        'roles' => 'array|exists:roles,name',
        'employment_type'  => 'nullable|in:permanent,contract',
        'department'       => 'nullable|in:office,workshop,security'
    ]);

     // Update User table
     $user->update([
        'name' => $request->full_name,
        'email' => $request->email,
    ]);

    // Update password if present
    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
        $user->save();
    }

    // Update Employee table
    if ($user->employee) {
        $user->employee->update([
            'sort_order' => $request->sort_order,
            'designation' => $request->designation,
            'base_salary' => $request->base_salary,
            'epf_number' => $request->epf_number,
            'join_date' => $request->join_date,
            'employment_status' => $request->employment_status,
            'employment_type' => $request->employment_type,
            'department' => $request->department
        ]);
    }

        $user->syncRoles($request->input('roles', [])); // empty array clears roles

    return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
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
            'epf_number' => 'nullable|string',
            'join_date' => 'required|date',
            'employment_status' => 'required|in:Active,Inactive,Resigned,Terminated',
            'employment_type'  => 'nullable|in:permanent,contract',
            'department'       => 'nullable|in:office,workshop,security'
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
                'epf_number' => $data['epf_number'],
                'join_date' => $data['join_date'],
                'employment_status' => $data['employment_status'],
                'employment_type' => $data['employment_type'],
                'department' => $data['department']
            ]);

            DB::commit();

            return redirect()->route('employees.index')->with('success', 'Employee registered successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->withErrors(['error' => 'Registration failed. Please try again.']);
        }
    }
}