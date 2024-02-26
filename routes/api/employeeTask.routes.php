
<?php

use Illuminate\Support\Facades\Route;

Route::get('total-employeetasks', [\App\Http\Controllers\EmployeeTasksController::class, 'showEmployeeTasks']);
Route::get('total-employeeintask/{tasks_id}', [\App\Http\Controllers\EmployeeTasksController::class, 'showEmployeeByIdTask']);
Route::get('total-taskinemployee/{employee_id}', [\App\Http\Controllers\EmployeeTasksController::class, 'showTotalTaskByIdEmployee']);
