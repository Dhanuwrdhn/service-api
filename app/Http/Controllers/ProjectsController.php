<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\EmployeeProject;
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
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'project_status' => 'nullable|in:onPending,workingOnIt,Completed',
        'percentage' => 'nullable|string',
        'total_task_completed' => 'nullable|string',
        'total_task_created' => 'nullable|string',
        'assign_to' => 'required|array', // Tambahkan validasi untuk assign_to sebagai array
        'assign_to.*' => 'exists:mg_employee,id', // Tambahkan validasi untuk setiap item di assign_to
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

        $project = Project::create($data);

        // Proses assign to
        $assigneesIds = $request->input('assign_to');

        // Memeriksa apakah setiap assign_to ada dalam data karyawan (employee)
        foreach ($assigneesIds as $assigneeId) {
            if (!Employees::find($assigneeId)) {
                throw new \Exception("Karyawan dengan ID $assigneeId tidak ditemukan.");
            }

            // Simpan ke mg_employee_project menggunakan model EmployeeProject
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
  public function update(Request $request, $id)
{
    $rules = [
        'project_name' => 'required|string',
        'team_id' => 'required|exists:mg_teams,id',
        'role_id' => 'required|exists:mg_roles,id',
        'jobs_id' => 'required|exists:mg_jobs,id',
        'assign_by' => 'required|exists:mg_employee,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'project_status' => 'nullable|in:Ongoing,workingOnIt,Completed',
        'percentage' => 'nullable|string',
        'total_task_completed' => 'nullable|string',
        'total_task_created' => 'nullable|string',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => $validator->errors()
        ], 400);
    }

    try {
        DB::beginTransaction();

        $project = Project::find($id);

        if (!$project) {
            throw new \Exception('Proyek tidak ditemukan.');
        }

        $project->update($request->all());

        // Update assignees (contoh: string dipisahkan koma)
        $assigneesIds = $request->input('assign_to', []);
        $project->employeeAssignees()->sync($assigneesIds);

        DB::commit();

        return response()->json([
            'status' => 'success',
            'data' => $project
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal memperbarui proyek. ' . $e->getMessage()
        ], 500);
    }
}


    public function destroy($id)
    {
        $projects = Project::find($id);
        if (!$projects) {
            return response()->json([
                'status' => 'error',
                'message' => 'project not found'
            ], 404);
        }
        $projects->delete();
    }

    public function updateProjectStatus(Request $request, $id)
{
    $rules = [
        'project_status' => 'required|in:onPending,workingOnIt,Completed',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => $validator->errors(),
        ], 400);
    }

    try {
        DB::beginTransaction();

        $project = Project::find($id);

        if (!$project) {
            throw new \Exception('Project not found');
        }

        $oldStatus = $project->project_status; // Simpan status proyek sebelum pembaruan

        $project->update($request->only('project_status'));

        // Bandingkan status sebelum dan setelah pembaruan
        if ($oldStatus === $project->project_status) {
            return response()->json([
                'status' => 'success',
                'message' => 'No changes detected in project status',
                'data' => $project->toArray(),
            ]);
        }

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'Project updated successfully',
            'data' => $project->toArray(),
        ]);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update project: ' . $e->getMessage(),
        ], 500);
    }
}

}
