<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Employees;
use App\Models\Project;
use App\Models\EmployeeProject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeTasksController extends Controller
{
public function showEmployeeTasks()
{
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
    public function showEmployeeTasksbyId($id){

        $employeeTask = DB::table('mg_employee_tasks')
        ->join('mg_employee', 'mg_employee.id', '=', 'mg_employee_tasks.employee_id')
        ->join('mg_projects', 'mg_projects.id', '=', 'mg_employee_tasks.project_id')
        ->join('mg_tasks', 'mg_tasks.id', '=', 'mg_employee_tasks.tasks_id')
        ->select('mg_employee_tasks.*', 'mg_tasks.*', 'mg_projects.*')
        ->where('mg_employee.id', $id) // Filter berdasarkan ID karyawan
        ->get();

    if ($employeeTask->isEmpty()) {
        return response()->json([
            'status' => 'error',
            'message' => 'No employee projects found for the given employee ID.'
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'employeeProjects' => $employeeTask
    ]);
}


}
