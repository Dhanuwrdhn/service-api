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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken as AccessToken;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Carbon;

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
    public function create(Request $request)
    {
        $rules = [
            'role_id' => 'required|integer',
            'jobs_id' => 'required|integer',
            'team_id' => 'required|integer',
            'employee_name' => 'required|string',
            'date_of_birth' => 'date',
            'age' => 'string',
            'mobile_number' => 'string',
            'gender' => 'in:Male,Female',
            'religion' => 'string',
            'npwp_number' => 'string',
            'identity_number' => 'string',
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

    // Start database transaction
        DB::beginTransaction();

        try {
            // Generate email from employee_name
            $nameParts = explode(' ', $data['employee_name']);
            $firstName = $nameParts[0];
            $middleName = '';
            $lastName = '';
            if (count($nameParts) > 1) {
                $lastName = end($nameParts);
                if (count($nameParts) > 2) {
                    $middleName = $nameParts[1];
                }
            } else {
                $lastName = $nameParts[0];
            }

            $email = ($middleName != '') ? $middleName . '.' : $firstName . '.';
            $email .= $lastName . '@innovation.co.id';

            // Check if username already exists, if yes, add a number after the username
            $username = ($middleName != '') ? strtolower($middleName) : strtolower($firstName);
            $count = 1;
            $originalUsername = $username;
            while (Employees::where('username', $username)->exists()) {
                $username = $originalUsername . $count;
                $count++;
            }
            $data['username'] = $username;
            $data['email'] = $email;

            // Generate password from date_of_birth
            $dobFormatted = date_create_from_format('Y-m-d', $data['date_of_birth'])->format('dmY');
            $password = $dobFormatted;

            // Hash the password and create employee
            $data['password'] = Hash::make($password);
            $employee = Employees::create($data);

            // Commit the transaction if all steps are successful
            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => $employee
            ], 200);
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollback();

            return response()->json([
                'status' => 'error',
            'message' => 'Failed to create employee: ' . $e->getMessage()
            ], 500);
        }
    }

    // update employee
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
    public function destroy($id){

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
    // login
    public function login(Request $request) {
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

        // check credentials
        $employee = Employees::where('username', $credentials['username'])->first();
        if(!($employee && Hash::check($credentials['password'], $employee->password))){
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }

        $currentToken = $employee->tokens()->latest()->first();

        //check if token expired
        $expiresAt = Carbon::parse($currentToken->expires_at);
        if ($expiresAt->isPast()) {
            // If the token is expired, delete it
            $currentToken->delete();

            // Create a new token with a new expiration date
            $newToken = $employee->createToken('authToken', ['*']);
            $expiresAt = now()->addHours(24)->toDateTimeString();

            // Set the new expiration date for the newly created token
            $newToken->update(['expires_at' => $expiresAt]);

            return response()->json([
                'status' => 'login success',
                'token' => $newToken->plainTextToken,
                'id_employee' => $employee->id,
                'username_employee' => $employee->username,
                'roleId_employee' => $employee->role_id,
                'expires_at' => $expiresAt,
            ]);
        }else {
            // If the token is not expired, return the existing token
            return response()->json([
                'message' => 'not expired',
                'token' => $currentToken->plainTextToken,
            ]);
        }
    }

    public function getAccessToken($tokenId) {
        $accessToken = AccessToken::find($tokenId);

        if (!$accessToken) {
            return response()->json(['message' => 'Access token not found'], 404);
        }

        // Menghitung waktu kedaluwarsa token
        $expiresAt = Carbon::createFromTimeString($accessToken->expires_at);
        $expiresIn = $expiresAt->diffForHumans();

        return response()->json([
            'access_token' => [
                'token' => $accessToken->token,
            'expires_at' => $expiresIn,
        // tambahkan informasi lain yang diperlukan
            ]
        ]);
    }

    //    public function refreshToken(Request $request)
// {
//     $employee = $request->user();

//     if (!$employee) {
//         return response()->json([
//             'status' => 'error',
//             'message' => 'Unauthorized',
//         ], 401);
//     }

//     // Hapus semua token pengguna
//     $employee->tokens()->delete();

//     // Buat token baru
//     $token = $employee->createToken('authToken')->plainTextToken;
//     // Simpan token baru di database bersama dengan informasi pengguna yang sesuai
//     $employee->update(['access_token' => $token]);

//     return response()->json([
//         'status' => 'success',
//         'token' => $token,
//         'message' => 'Token refreshed successfully.',
//     ]);
// }

    public function logOut($id){
        // Temukan pengguna berdasarkan ID
        $user = Employees::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'employee not found'
            ]);
        }
        if ($user) {
        // Revoke semua token yang terkait dengan pengguna
        $user->tokens()->delete();
        // Atau, jika Anda ingin hanya menonaktifkan token akses di database (tanpa menghapusnya dari penyedia token)
        // $user->tokens->each->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil'
        ]);
    } else {
        // Jika pengguna tidak ditemukan
        return response()->json([
            'status' => 'error',
            'message' => 'User tidak ditemukan'
        ], 404);
    }
    }
}
