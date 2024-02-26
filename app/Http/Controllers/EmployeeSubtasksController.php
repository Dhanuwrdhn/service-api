<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employees;
use App\Models\Project;
use App\Models\EmployeeSubtasks;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeSubtasksController extends Controller
{
     // show employee subtask
    public function showEmployeeSubTasks(){

        $employeeSubTask = DB::table('mg_employee_subtask')
            ->join('mg_employee', 'mg_employee.id', '=', 'mg_employee_subtask.employee_id')
            ->join('mg_tasks', 'mg_tasks.id', '=', 'mg_employee_subtask.tasks_id')
            ->select('mg_employee_subtask.*', 'mg_tasks.*',)
            ->get();

        if ($employeeSubTask->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No employee SubTask found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'employeeProjects' => $employeeSubTask
        ]);
    }
    // show subtask employee by id
    public function showEmployeeSubtaskByIdSubtask($subtask_id){

        $employeeSubtask = EmployeeSubtasks::join('mg_employee', 'mg_employee.id', '=', 'mg_employee_subtask.employee_id')
            ->join('mg_tasks', 'mg_employee_subtask.tasks_id', '=', 'mg_tasks.id')
            ->where('mg_employee_subtask.subtasks_id', $subtask_id)
            ->select('mg_employee_subtask.*', 'mg_employee.*', 'mg_tasks.*')
            ->get();

        if ($employeeSubtask->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No employee SubTask found for the specified employee ID.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'employeeProjects' => $employeeSubtask
        ]);
    }
    // show employee subtasks by id
    public function showSubtaskEmployeeByIdEmployee($employee_id){

        $employeeSubtask = EmployeeSubtasks::join('mg_employee', 'mg_employee.id', '=', 'mg_employee_subtask.employee_id')
            ->join('mg_tasks', 'mg_employee_subtask.tasks_id', '=', 'mg_tasks.id')
            ->join('mg_sub_tasks', 'mg_employee_subtask.subtasks_id', '=', 'mg_sub_tasks.id')
            ->where('mg_employee_subtask.employee_id', $employee_id)
            ->select('mg_employee_subtask.*', 'mg_employee.*', 'mg_tasks.*', 'mg_sub_tasks.*')
            ->get();

        if ($employeeSubtask->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No employee SubTask found for the specified employee ID.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'employeeSubTask' => $employeeSubtask
        ]);
    }
}
