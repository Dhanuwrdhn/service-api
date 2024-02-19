
<?php

use Illuminate\Support\Facades\Route;

Route::post('employees', [\App\Http\Controllers\EmployeesController::class, 'create']);
Route::put('employees/{id}', [\App\Http\Controllers\EmployeesController::class, 'update']);
Route::get('employees', [\App\Http\Controllers\EmployeesController::class, 'index']);
Route::get('employees/{id}', [\App\Http\Controllers\EmployeesController::class, 'show']);
Route::get('access-tokens/{tokenId}', [\App\Http\Controllers\EmployeesController::class, 'getAccessToken']);
Route::delete('employees/{id}', [\App\Http\Controllers\EmployeesController::class, 'destroy']);
