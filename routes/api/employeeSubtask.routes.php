
<?php

use Illuminate\Support\Facades\Route;


Route::get('total-employeesubtasks', [\App\Http\Controllers\EmployeeSubtasksController::class, 'showEmployeeSubTasks']);
Route::get('total-employeesubtasks/{subtask_id}', [\App\Http\Controllers\EmployeeSubtasksController::class, 'showEmployeeSubtaskByIdSubtask']);
Route::get('total-employeesubtasksbyemployee/{employeeid}', [\App\Http\Controllers\EmployeeSubtasksController::class, 'showSubtaskEmployeeByIdEmployee']);
