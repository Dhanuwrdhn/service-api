<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Employees;
use App\Models\Job;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmployeesController extends Controller
{
    //// getALL
    public function index(Request $request)
    {
        $employee = Employees::all();
        $employee->makeHidden(['password']);
        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'employee not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => $employee
        ]);

    }
    // get by id
    public function show($id)
    {
        $employee = Employees::find($id);
        $employee->makeHidden(['password']);
        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'employee not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $employee
        ]);
    }
    // Create Employee
    public function create(Request $request)
    {
        $rules=[
          'role_id' => 'required|integer',
          'jobs_id' => 'required|integer',
          'team_id' => 'required|integer',
          'employee_name' => 'required|string',
          'date_of_birth' => 'date',
          'age' => 'string',
          'mobile_number' => 'string',
          'email' => 'required|email|unique:mg_employee,email',
          'username' => 'required|string',
          'password' => 'required|string|min:8',
          'gender' => 'in:Male,Female',
          'religion' => 'string',
          'npwp_number' => 'string',
          'identity_number' => 'string',
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }
        $roleId = $request->input('role_id');
        $roleId= Role::find($roleId);
        if(!$roleId){
            return response()->json([
                'status' => 'error',
                'message' => 'role not found'
            ], 404);
        }
        $jobId = $request->input('jobs_id');
        $jobId= Job::find($jobId);
        if(!$jobId){
            return response()->json([
                'status' => 'error',
                'message' => 'job not found'
            ], 404);
        }
        $teamId = $request->input('team_id');
        $teamId= Team::find($teamId);
        if(!$teamId){
            return response()->json([
                'status' => 'error',
                'message' => 'team not found'
            ], 404);
        }
        $data['password'] = Hash::make($data['password']);
        $employee = Employees::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $employee
        ], 200);
    }

    public function update(Request $request, $id){
        $rules=[
          'role_id' => 'required|integer',
          'jobs_id' => 'required|integer',
          'team_id' => 'required|integer',
          'employee_name' => 'required|string',
          'date_of_birth' => 'date',
          'age' => 'string',
          'mobile_number' => 'string',
          'email' => 'required|email|unique:mg_employee,email',
          'username' => 'required|string',
          'password' => 'required|string|min:8',
          'gender' => 'required|in:male,female',
          'religion' => 'string',
          'npwp_number' => 'string',
          'identity_number' => 'string',
        ];
        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }
        $employee = Employees::find($id);
        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found'
            ], 404);
        }
        $roleId = $request->input('role_id');
        if ($roleId) {
            $roleId = Role::find($roleId);
            if (!$roleId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'roles not found'
                ], 404);
            }
        }
        $jobId = $request->input('jobs_id');
        if ($jobId) {
            $jobId = Job::find($jobId);
            if (!$jobId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'jobs not found'
                ], 404);
            }
        }
        $teamId = $request->input('team_id');
        if ($teamId) {
            $teamId = Team::find($teamId);
            if (!$teamId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'teams not found'
                ], 404);
            }
        }
        $employee->password = Hash::make($request->input('password'));
        $employee->fill($data);
        $employee->save();

        return response()->json([
            'status' => 'success',
            'data' => $employee
        ]);
    }
    //delete
    public function destroy($id)
    {
        $employee = Employees::find($id);

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'employee not found'
            ]);
        }

        $employee->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'employee deleted'
        ]);
    }
    public function login(Request $request)
    {
    $credentials = $request->only('username', 'password');

    $validator = Validator::make($credentials, [
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => $validator->errors()
        ], 400);
    }

    $employee = Employees::where('username', $credentials['username'])->first();

    if ($employee && Hash::check($credentials['password'], $employee->password)) {
        $token = $employee->createToken('authToken')->plainTextToken;
          // Simpan token di database bersama dengan informasi pengguna yang sesuai
        $employee->update(['access_token' => $token]);

        return response()->json([
            'status' => 'login success',
            'token' => $token,
            'id_employee'=> $employee->id,// Include user details if needed
            'username_employee'=> $employee->username,// Include user details if needed
        ]);
    } else {
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials',
        ], 401);
    }
}
   public function refreshToken(Request $request)
{
    $employee = $request->user();

    if (!$employee) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized',
        ], 401);
    }

    // Hapus semua token pengguna
    $employee->tokens()->delete();

    // Buat token baru
    $token = $employee->createToken('authToken')->plainTextToken;
    // Simpan token baru di database bersama dengan informasi pengguna yang sesuai
    $employee->update(['access_token' => $token]);

    return response()->json([
        'status' => 'success',
        'token' => $token,
        'message' => 'Token refreshed successfully.',
    ]);
}


}
