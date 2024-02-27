
<?php

use Illuminate\Support\Facades\Route;

Route::get('total-employeeprojects', [\App\Http\Controllers\EmployeeProjectController::class, 'showEmployeeProjects']);
Route::get('total-employeeinproject/{project_id}', [\App\Http\Controllers\EmployeeProjectController::class, 'showTotalProjectByIdProject']);
Route::get('total-projectinemployee/{employee_id}', [\App\Http\Controllers\EmployeeProjectController::class, ' showTotalEmployeeProjectByIdEmployee']);