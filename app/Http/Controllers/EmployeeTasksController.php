<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Employees;
use App\Models\Project;
use App\Models\Tasks;
use App\Models\EmployeeTasks;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeTasksController extends Controller
{
    public function showEmployeeTasks(){
        $employeeTask = DB::table('mg_employee_tasks')
            ->join('mg_employee', 'mg_employee.id', '=', 'mg_employee_tasks.employee_id')
            ->join('mg_projects', 'mg_projects.id', '=', 'mg_employee_tasks.project_id')
            ->join('mg_tasks', 'mg_tasks.id', '=', 'mg_employee_tasks.tasks_id')
            ->select('mg_employee_tasks.*', 'mg_tasks.*', 'mg_projects.*')
            ->get();

        if ($employeeTask->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No employee task found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'employeeProjects' => $employeeTask
        ]);
    }
        public function showTotalTaskByIdTask($tasks_id){
            $employeeTasks = EmployeeTasks::join('mg_employee', 'mg_employee.id', '=', 'mg_employee_tasks.employee_id')
                ->join('mg_projects', 'mg_employee_tasks.project_id', '=', 'mg_projects.id')
                ->join('mg_tasks', 'mg_tasks.id', '=', 'mg_employee_tasks.tasks_id')
                ->where('mg_employee_tasks.tasks_id', $tasks_id)
                ->select('mg_employee_tasks.*', 'mg_employee.*', 'mg_projects.*', 'mg_tasks.*' )
                ->get();

            if ($employeeTasks->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No employee task found for the specified employee ID.'
                ], 404);
        }

            return response()->json([
                'status' => 'success',
                'employeeTasks' => $employeeTasks
            ]);
    }
        public function showTotalTaskByIdEmployee($employee_id){
            $employeeTasks = EmployeeTasks::join('mg_employee', 'mg_employee.id', '=', 'mg_employee_tasks.employee_id')
                ->join('mg_projects', 'mg_employee_tasks.project_id', '=', 'mg_projects.id')
                ->join('mg_tasks', 'mg_tasks.id', '=', 'mg_employee_tasks.tasks_id')
                ->where('mg_employee_tasks.employee_id', $employee_id)
                ->select('mg_employee_tasks.*', 'mg_employee.*', 'mg_projects.*', 'mg_tasks.*' )
                ->get();

            if ($employeeTasks->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No employee task found for the specified employee ID.'
                ], 404);
        }

            return response()->json([
                'status' => 'success',
                'employeeTasks' => $employeeTasks
            ]);
    }

}
