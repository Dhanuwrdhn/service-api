<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
// API JOBS
Route::get('jobs', [\App\Http\Controllers\JobsController::class, 'index']);
Route::get('jobs/{id}', [\App\Http\Controllers\JobsController::class, 'show']);
Route::post('jobs', [\App\Http\Controllers\JobsController::class, 'create']);
Route::put('jobs/{id}', [\App\Http\Controllers\JobsController::class, 'update']);
Route::delete('jobs/{id}', [\App\Http\Controllers\JobsController::class, 'destroy']);
// API TEAMS
Route::get('teams', [\App\Http\Controllers\TeamsController::class, 'index']);
Route::post('teams', [\App\Http\Controllers\TeamsController::class, 'create']);
Route::put('teams/{id}', [\App\Http\Controllers\TeamsController::class, 'update']);
Route::delete('teams/{id}', [\App\Http\Controllers\TeamsController::class, 'destroy']);
Route::get('teams/{id}', [\App\Http\Controllers\TeamsController::class, 'show']);
// API CLIENT
Route::get('clients', [\App\Http\Controllers\ClientsController::class, 'index']);
Route::post('clients', [\App\Http\Controllers\ClientsController::class, 'create']);
Route::put('clients/{id}', [\App\Http\Controllers\ClientsController::class, 'update']);
Route::delete('clients/{id}', [\App\Http\Controllers\ClientsController::class, 'destroy']);
Route::get('clients/{id}', [\App\Http\Controllers\ClientsController::class, 'show']);
//API ROLE
Route::get('roles', [\App\Http\Controllers\RolesController::class, 'index']);
Route::post('roles', [\App\Http\Controllers\RolesController::class, 'create']);
Route::put('roles/{id}', [\App\Http\Controllers\RolesController::class, 'update']);
Route::delete('roles/{id}', [\App\Http\Controllers\RolesController::class, 'destroy']);
Route::get('roles/{id}', [\App\Http\Controllers\RolesController::class, 'show']);
// API PROJECT
Route::get('projects', [\App\Http\Controllers\ProjectsController::class, 'index']);
Route::post('projects', [\App\Http\Controllers\ProjectsController::class, 'create']);
Route::put('projects/{id}', [\App\Http\Controllers\ProjectsController::class, 'update']);
Route::delete('projects/{id}', [\App\Http\Controllers\ProjectsController::class, 'destroy']);
Route::put('projects-status/{id}', [\App\Http\Controllers\ProjectsController::class, 'updateProjectStatus']);
Route::get('projects/{id}', [\App\Http\Controllers\ProjectsController::class, 'show']);
//API EMPLOYEE
Route::post('employees', [\App\Http\Controllers\EmployeesController::class, 'create']);
Route::put('employees/{id}', [\App\Http\Controllers\EmployeesController::class, 'update']);
Route::get('employees', [\App\Http\Controllers\EmployeesController::class, 'index']);
Route::get('employees/{id}', [\App\Http\Controllers\EmployeesController::class, 'show']);
Route::delete('employees/{id}', [\App\Http\Controllers\EmployeesController::class, 'destroy']);
// API TASK
Route::post('create-tasks', [\App\Http\Controllers\TasksController::class, 'create']);
Route::get('show-tasks', [\App\Http\Controllers\TasksController::class, 'index']);
Route::put('update-status/{id}', [\App\Http\Controllers\TasksController::class, 'updateStatus']);
// API SUBTASKS
Route::post('create-subtasks', [\App\Http\Controllers\SubTaskController::class, 'createSubTask']);
Route::get('show-subtasks', [\App\Http\Controllers\SubTaskController::class, 'index']);
Route::get('show-subtasks/{id}', [\App\Http\Controllers\SubTaskController::class, 'showSubTask']);

// API TOTAL EMPLOYEE PROJECT
Route::post('create-emproject', [\App\Http\Controllers\EmployeeProjectController::class, 'create']);
Route::get('total-projects', [\App\Http\Controllers\EmployeeProjectController::class, 'showEmployeeProjects']);
Route::get('total-projects/{id}', [\App\Http\Controllers\EmployeeProjectController::class, 'showEmployeeProjectById']);
// API TOTAL EMPLOYEE TASK
Route::get('total-tasks', [\App\Http\Controllers\EmployeeSubTaskController::class, 'showEmployeeTasks']);

// API LOGIN
Route::post('login', [\App\Http\Controllers\EmployeesController::class, 'login']);
// // refresh-token
// Route::middleware('auth:sanctum')->group(function () {
// /
// Route::get('current-access-token', function () {
//         $employee = auth()->user(); // Use auth() helper to get the authenticated user

//         // Mendapatkan current access token
//         $currentAccessToken = $employee->currentAccessToken();

//         if ($currentAccessToken) {
//             return response()->json([
//                 'token' => $currentAccessToken->plainTextToken,
//             ]);
//         } else {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Employee does not have a valid access token.',
//             ], 401);
//         }
//     });


//     Route::post('refresh-token', [\App\Http\Controllers\EmployeesController::class, 'refreshToken']);
// });
