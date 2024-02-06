<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Job;
use App\Models\Role;
use App\Models\Team;
// use App\Models\Client;
use App\Models\Task;
use App\Models\Employees;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProjectsController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        return response()->json([
            'status' => 'success',
            'data' => $projects
        ]);
    }

    public function show($id)
    {
        $projects = Project::find($id);
        if (!$projects) {
            return response()->json([
                'status' => 'error',
                'message' => 'projects not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $projects
        ]);
    }
    public function create(Request $request)
    {
    $rules = [
        'project_name' => 'required|string',
        'team_id' => 'required|exists:mg_teams,id',
        'role_id' => 'required|exists:mg_roles,id',
        'jobs_id' => 'required|exists:mg_jobs,id',
        'assign_by' => 'required|exists:mg_employee,id',
        'assign_to' => 'required|array', // Ubah menjadi array
        'assign_to.*' => 'exists:mg_employee,id', // Validasi setiap ID dalam array
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'project_status' => 'nullable|in:Ongoing,workingOnIt,Completed',
        'percentage' => 'nullable|string',
        'total_task_completed' => 'nullable|string',
        'total_task_created' => 'nullable|string',
    ];

    $data = $request->all();

    $validator = Validator::make($data, $rules);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => $validator->errors()
        ], 400);
    }

    try {
        DB::beginTransaction();

        // Validasi dan ambil data terkait
        $roles = Role::find($request->input('role_id'));
        $jobs = Job::find($request->input('jobs_id'));
        $teams = Team::find($request->input('team_id'));
        $assignBy = Employees::find($request->input('assign_by'));

        if (!$roles || !$jobs || !$teams || !$assignBy) {
            throw new \Exception('Data terkait tidak ditemukan.');
        }

        // Buat proyek
        $project = Project::create($data);

        // Proses assign to (contoh: string dipisahkan koma)
        $assigneesIds = $request->input('assign_to');
        $project->employeeAsignees()->attach($assigneesIds);

        // Simpan ke mg_employee_project menggunakan model EmployeeProject
        foreach ($assigneesIds as $assigneeId) {
            EmployeeProject::create([
                'employee_id' => $assigneeId,
                'project_id' => $project->id,
            ]);
        }

        DB::commit();

        return response()->json([
            'status' => 'success',
            'data' => $project
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal membuat proyek. ' . $e->getMessage()
        ], 500);
    }
}
    // update
    public function update(Request $request, $id){
         $rules=[
            'project_name' => 'required|string',
            'role_id' => 'required',
            'jobs_id' => 'required',
            'team_id' => 'required',
            'client_id' => 'required',
            'start_date' => 'date',
            'end_date' => 'date',
            'percentage'=>'string',
            'project_status' => 'required|in:Ongoing,workingOnIt,Completed',
            'total_task_completed' => 'string',
            'total_task_created' => 'string',
        ];
        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }
        $projects = Projects::find($id);
        if (!$projects) {
            return response()->json([
                'status' => 'error',
                'message' => 'project not found'
            ], 404);
        }
        $roleId = $request->input('role_id');
        if ($roleId) {
            $roleId = Roles::find($roleId);
            if (!$roleId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'roles not found'
                ], 404);
            }
        }
        $jobId = $request->input('jobs_id');
        if ($jobId) {
            $jobId = Jobs::find($jobId);
            if (!$jobId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'jobs not found'
                ], 404);
            }
        }
        $teamId = $request->input('team_id');
        if ($teamId) {
            $teamId = Teams::find($teamId);
            if (!$teamId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'teams not found'
                ], 404);
            }
        }
        $clientId = $request->input('client_id');
        if ($clientId) {
            $clientId = Client::find($clientId);
            if (!$clientId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'client not found'
                ], 404);
            }
        }
         $projects->fill($data);
        $projects->save();

        return response()->json([
            'status' => 'success',
            'data' => $projects
        ]);
    }

    public function destroy($id)
    {
        $projects = Projects::find($id);
        if (!$projects) {
            return response()->json([
                'status' => 'error',
                'message' => 'project not found'
            ], 404);
        }
        $projects->delete();
    }

    public function updateProjectStatus(Request $request, $id){
        $rules = [
          'project_status' => 'required|in:Ongoing,workingOnIt,Completed',
        ];
        $data = $request->only('project_status');
        $project = Project::find($id);

    if (!$project) {
        return response()->json([
            'status' => 'error',
            'message' => 'Project not found',
        ], 404);
    }

    // Lanjutkan dengan logika pembaruan setelah memastikan $project bukan null
    $project->update($data);

    return response()->json([
        'status' => 'success',
        'message' => 'Project updated successfully',
        'data' => $project->toArray(),
    ]);
    }

}
