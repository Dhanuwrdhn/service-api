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
    public function showEmployeeProjectById($id)
    {
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
    
    public function create(Request $request)
    {
        $rules = [
            'employee_id' => 'required',
            'project_id' => 'required',
        ];
        $data = $request->all();
        $validator = Validator::make($data, $rules);

        if ($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $employee_id = $request->input('employee_id');
        $employees = Employees::find($employee_id);
        if(!$employees){
            return response()->json([
                'status' => 'error',
                'message' => 'employee not found'
            ],400);
        }
        $project_id = $request->input('project_id');
        $projects = Project::find($project_id);
        if(!$employees){
            return response()->json([
                'status' => 'error',
                'message' => 'project not found'
            ],400);
        }

        $employeeProjects = EmployeeProject::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $projects
        ], 200);
    }
}
