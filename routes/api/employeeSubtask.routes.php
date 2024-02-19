
<?php

use Illuminate\Support\Facades\Route;


Route::get('total-employeesubtasks', [\App\Http\Controllers\EmployeeSubTasksController::class, 'showEmployeeSubTasks']);
Route::get('total-employeesubtasks/{subtask_id}', [\App\Http\Controllers\EmployeeTasksController::class, 'showEmployeeSubtaskByIdSubtask']);
Route::get('total-employeesubtasks/{employeeid}', [\App\Http\Controllers\EmployeeTasksController::class, 'showSubtaskEmployeeByIdEmployee']);