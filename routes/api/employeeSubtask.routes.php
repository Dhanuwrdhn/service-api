
<?php

use Illuminate\Support\Facades\Route;


Route::get('total-employeesubtasks', [\App\Http\Controllers\EmployeeSubtasksController::class, 'showEmployeeSubTasks']);
Route::get('total-employeeinsubtasks/{subtask_id}', [\App\Http\Controllers\EmployeeSubtasksController::class, 'showEmployeeSubtaskByIdSubtask']);
Route::get('total-subtasksinemployee/{employeeid}', [\App\Http\Controllers\EmployeeSubtasksController::class, 'showSubtaskEmployeeByIdEmployee']);
