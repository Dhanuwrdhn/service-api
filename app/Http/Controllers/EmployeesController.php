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
        public function updateEmployee(Request $request, $id){
        $rules = [
            'employee_name' => 'sometimes|string',
            'date_of_birth' => 'sometimes|date',
            'age' => 'string',
            'mobile_number' => 'string',
            'email' => 'sometimes|email|unique:mg_employee,email',
            'username' => 'sometimes|string',
            'gender' => 'in:male,female',
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

        try {
            DB::beginTransaction();

            // Update data employee
            $employee->fill($data);
            $employee->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => $employee
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update employee data: ' . $e->getMessage()
            ], 500);
        }
    }

    //delete
    public function destroy($id){

        $employee = Employees::find($id);

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'employee not found'
            ], 404);
        }

        $employee->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'employee deleted'
        ], 200);
    }
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
    if(!$employee || !Hash::check($credentials['password'], $employee->password)){
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials',
        ], 401);
    }

    // Get the current access token
    $token = $employee->createToken('authToken')->plainTextToken;

    // Set token expiration time to 24 hours (1 day) from now
    $tokenModel = AccessToken::findToken($token);
    $tokenModel->update(['expires_at' => now()->addDay()]);

    return response()->json([
        'status' => 'login success',
        'token' => $token,
        'id_employee' => $employee->id,
        'username_employee' => $employee->username,
        'roleId_employee' => $employee->role_id,
        // You can set custom expiration time here if needed
        'expires_at' => now()->addDay()->toDateTimeString(), // 24 hours from now
    ], 200);
}

    public function getAccessToken($tokenId) {
        $accessToken = AccessToken::find($tokenId);

        if (!$accessToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access token not found'
            ], 404);
        }

        // Menghitung waktu kedaluwarsa token
        $expiresAt = Carbon::createFromTimeString($accessToken->expires_at);
        $expiresIn = $expiresAt->diffForHumans();

        return response()->json([
            'status' => 'success',
            'access_token' => [
                'token' => $accessToken->token,
            'expires_at' => $expiresIn,
        // tambahkan informasi lain yang diperlukan
            ]
        ],200);
    }
        public function logOut($id){
            // Temukan pengguna berdasarkan ID
            $user = Employees::find($id);

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'employee not found'
                ], 404);
            }
            if ($user) {
            // Revoke semua token yang terkait dengan pengguna
            $user->tokens()->delete();
            // Atau, jika Anda ingin hanya menonaktifkan token akses di database (tanpa menghapusnya dari penyedia token)
            // $user->tokens->each->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Logout berhasil'
            ], 200);
        }
    }

    //change password
    public function changePassword(Request $request, $id) {
        try{
            $data = $request->validate([
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*()-_=+{};:,<.>ยง~]).*$/',
                ],
            ]);

            $employee = Employees::find($id);

            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee not found'
                ], 404);
            }

            // Ensure the new password meets the specified criteria
            // Ensure the new password meets the specified criteria
            $newPassword = $data['password'];
            if (!preg_match('/^(?=.*[A-Z])(?=.*[!@#$%^&*()-_=+{};:,<.>ยง~]).*$/', $newPassword)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Password must contain at least 8 characters, one uppercase letter, and one symbol'
                ], 400);
            }


            // Jika semua validasi berhasil, lanjutkan dengan mengubah password dan menyimpan data employee
            $employee->password = Hash::make($newPassword);
            $employee->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Password berhasil diubah'
            ], 200);


        }catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to change password: ' . $e->getMessage()
            ], 500);
        }


    }
}
