
<?php

use Illuminate\Support\Facades\Route;


// API LOGOUT
Route::delete('logOut/{id}', [\App\Http\Controllers\EmployeesController::class, 'logOut']);

// API LOGIN
Route::post('login', [\App\Http\Controllers\EmployeesController::class, 'login']);