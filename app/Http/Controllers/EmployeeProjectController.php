<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employees;
use App\Models\Project;
use App\Models\EmployeeProject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeProjectController extends Controller
{
    public function showEmployeeProjects()
    {
        $employeeProjects = EmployeeProject::join('mg_employee', 'mg_employee.id', '=', 'mg_employee_project.employee_id')
            ->join('mg_projects', 'mg_employee_project.project_id', '=', 'mg_projects.id')
            ->select('mg_employee_project.*', 'mg_employee.*', 'mg_projects.*')
            ->get();

      if ($employeeProjects->isEmpty()) {
        return response()->json([
            'status' => 'error',
            'message' => 'No employee projects found.'
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'employeeProjects' => $employeeProjects
    ]);
}
     public function showProjectById($id){
        $employeeProjects = EmployeeProject::join('mg_employee', 'mg_employee.id', '=', 'mg_employee_project.employee_id')
            ->join('mg_projects', 'mg_employee_project.project_id', '=', 'mg_projects.id')
            ->where('mg_projects.id', $id)
            ->select('mg_employee_project.*', 'mg_employee.*', 'mg_projects.*')
            ->get();

        if ($employeeProjects->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No employee projects found for the specified employee ID.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'employeeProjects' => $employeeProjects
        ]);
    }

    // show employee in project by id
        public function showEmployeeById($id){
        $employeeProjects = EmployeeProject::join('mg_employee', 'mg_employee.id', '=', 'mg_employee_project.employee_id')
            ->join('mg_projects', 'mg_employee_project.project_id', '=', 'mg_projects.id')
            ->where('mg_employee.id', $id)
            ->select('mg_employee_project.*', 'mg_employee.*', 'mg_projects.*')
            ->get();

        if ($employeeProjects->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No employee projects found for the specified employee ID.'
            ], 404);
    }

        return response()->json([
            'status' => 'success',
            'employeeProjects' => $employeeProjects
        ]);
    }
}
