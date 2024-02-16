<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employees;
use App\Models\Project;
use App\Models\EmployeeProject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeSubtasksController extends Controller
{
     // show employee subtask
    public function showEmployeeSubTasks()
    {

    $employeeSubTask = DB::table('mg_employee_subtask')
        ->join('mg_employee', 'mg_employee.id', '=', 'mg_employee_subtask.employee_id')
        ->join('mg_tasks', 'mg_tasks.id', '=', 'mg_employee_subtask.tasks_id')
        ->select('mg_employee_subtask.*', 'mg_tasks.*',)
        ->get();

    if ($employeeTask->isEmpty()) {
        return response()->json([
            'status' => 'error',
            'message' => 'No employee tasks found.'
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'employeeProjects' => $employeeTask
    ]);
}
}
