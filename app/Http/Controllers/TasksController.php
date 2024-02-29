<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\Employees;
use App\Models\EmployeeTasks;
use App\Models\EmployeeProject;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TasksController extends Controller
{
    //Get Tasks all
    public function index(){
        $tasks=Task::all();
        return response()->json([
            'success'=> 'success',
            'data' => $tasks
        ]);
    }

    // Show All Task by project id
    public function showByProject($projectId)
    {
        $tasks = Task::where('project_id', $projectId)->get();

        return response()->json([
            'status' => 'success',
            'data' => $tasks
        ]);
    }

    public function showTaskSpecific($taskid)
    {
        $tasks = Task::where('id', $taskid)->get();

        return response()->json([
            'status' => 'success',
            'data' => $tasks
        ]);
    }


    //Create Tasks
    public function createTask(Request $request)
{
    // Aturan validasi untuk input
    $rules = [
        'task_name' => 'required|string',
        'task_desc' => 'nullable|string',
        'start_date' => 'required|date',
        'end_date' => 'sometimes|date',
        'assign_by' => 'required|exists:mg_employee,id', // Pastikan assign_by adalah ID karyawan yang valid
        'assign_to' => 'required|array', // assign_to harus berupa array
        'assign_to.*' => [ // setiap item di assign_to harus ada dalam mg_employee_project dan terkait dengan proyek yang sesuai
            'exists:mg_employee_project,employee_id',
            function ($attribute, $value, $fail) use ($request) {
                // Validasi tambahan: pastikan karyawan terkait dengan proyek yang sesuai
                $projectId = $request->input('project_id');
                $employeeProject = EmployeeProject::where('employee_id', $value)
                                                   ->where('project_id', $projectId)
                                                   ->exists();
                if (!$employeeProject) {
                    $fail("Employee with ID $value is not associated with the specified project.");
                }
            },
        ],
        'percentage_task' => 'sometimes|in:0,5,10,15,20,25,30,35,40,45,50,55,60,65,70,75,80,85,90,95,100',
        'subtask_submit_status' => 'in:earlyFinish,finish,finish in delay,overdue',
        'task_image' => 'string|nullable',
        'task_reason' => 'string|nullable',
        'completed_date' => 'nullable|date',
        'task_status' => 'in:onPending,onReview,workingOnIt,Completed',// Pastikan task_status di antara nilai yang valid
    ];

    // Validasi input
        Validator::extend('not_assigned', function ($attribute, $value, $parameters, $validator) use ($request) {
            //Validate to check if employee already assigned with the same subtask name
            $subtask = SubTasks::join('mg_employee_tasks', 'mg_tasks.id', '=', 'mg_employee_tasks.tasks_id')
                                ->where('mg_tasks.task_name', $request->input('task_name'))
                                ->where('mg_employee_tasks.employee_id', $value)
                                ->exists();

            if ($subtask) {
                return false;
            }
            return true;

        }, 'The employee is already assigned to the subtask.');
    // Validasi input
    $validator = Validator::make($request->all(), $rules);

    // Jika validasi gagal, kembalikan respon dengan pesan error
    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => $validator->errors()
        ], 400);
    }

    try {
        // Memulai transaksi database
        DB::beginTransaction();

        // Mendapatkan proyek dan karyawan yang terlibat
        $project = Project::find($request->input('project_id'));
        $assignBy = Employees::find($request->input('assign_by')); // Ubah ke Employee

        // Pastikan proyek dan karyawan yang terlibat ditemukan
        if (!$project || !$assignBy) {
            return response()->json([
                'status' => 'error',
                'message' => 'project  and employee not found'
            ], 404);
        }
        //mengambil semua subtask dan mengambil jumlah persentase dan jumlahkan seluruhnya
        $totalTaskPercentage = Task::where('task_id', $request->input('task_id'))->sum('task_percentage');

        if($totalTaskPercentage + $request->input('subtask_percentage') > 100){
            return response()->json([
                'status' => 'error',
                $totalTaskPercentage,
                'message' => 'Total percentage of subtask is more than 100%'
            ], 400);
        }
        // Membuat tugas
        $task = Task::create($request->all());

        // Mengassign tugas kepada karyawan
        $assignedToIds = $request->input('assign_to');
        foreach ($assignedToIds as $assignedToId) {
            EmployeeTasks::create([
                'tasks_id' => $task->id,
                'project_id' => $project->id,
                'employee_id' => $assignedToId,
            ]);
        }

        // Menambahkan jumlah tugas yang dibuat ke dalam proyek
        $project->total_task_created += 1;
        //Mengganti status proyek menjadi onWorking
        $project->project_status = 'workingOnit';
        $project->save();


        // Mengembalikan respon sukses
        DB::commit();
        return response()->json([
            'status' => 'success',
            'message' => 'Task created successfully',
            'data' => $task
        ], 201);


    } catch (\Exception $e) {
        // Rollback transaksi database jika terjadi kesalahan
        DB::rollBack();

        // Respon dengan pesan error
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to create task. ' . $e->getMessage()
        ], 500);
    }
}
        //Edit task
        public function edittask(Request $request, $id){

            $rules = [
                'task_name' => 'sometimes|string',
                'task_description' => 'sometimes|string',
                'percentage_task' => 'sometimes|in:0,5,10,15,20,25,30,35,40,45,50,55,60,65,70,75,80,85,90,95,100',
                'assign_to' => 'required|array', // assign_to harus berupa array
                'assign_to.*' => [ // setiap item di assign_to harus ada dalam mg_employee_project dan terkait dengan proyek yang sesuai
                'exists:mg_employee_project,employee_id',
                function ($attribute, $value, $fail) use ($request) {
                    // Validasi tambahan: pastikan karyawan terkait dengan proyek yang sesuai
                    $projectId = $request->input('project_id');
                    $employeeProject = EmployeeProject::where('employee_id', $value)
                                                    ->where('project_id', $projectId)
                                                    ->exists();
                    if (!$employeeProject) {
                        $fail("Employee with ID $value is not associated with the specified project.");
                    }
                },
            ],
        ];

            // Validasi input
            $validator = Validator::make($request->all(), $rules);

            // Jika validasi gagal, kembalikan respon dengan pesan error
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], 400);
            }

            try{
                DB::beginTransaction();

                $task = Task::find($id);
                if(!$task){
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Subtask not found'
                    ], 404);
                }

                $validatedData = $request->validate([
                    'task_name' => 'sometimes|string',
                    'task_description' => 'sometimes|string',
                    'start_date' => 'sometimes|date',
                    'end_date' => 'sometimes|date',
                    'task_status' => 'sometimes|string',
                    'task_image' => 'string|nullable',
                    'task_reason' => 'string|nullable',
                    'completed_date' => 'nullable|date',
                ]);
                $task->update($validatedData);

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'task updated successfully',
                    'data' => $task
                ]);
            }catch(\Exception $e){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to edit task: ' . $e->getMessage()
                ], 500);
            }
        }

    //Update status Tasks
   public function updateStatus(Request $request, $id) {
    $rules = [
        'task_status' => 'required',
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => $validator->errors()
        ], 400);
    }

    $task = Task::find($id);

    if (!$task) {
        return response()->json([
            'status' => 'error',
            'message' => 'Task not found'
        ], 404);
    }

    $data = $request->only('task_status');

    DB::beginTransaction();

    $task->update($data);

    // Check if the task status is Completed, then update mg_projects
    if ($data['task_status'] == 'Completed') {
        $project = Project::find($task->project_id);

        if ($project) {
            $project->increment('total_task_completed');
            $projectPercentage =  $project->total_task_completed / $project->total_task_created * 100;
            $project->percentage = $projectPercentage;
            $project->save();
        }
    }

    DB::commit();
    return response()->json([
        'status' => 'success',
        'data' => $task
    ], 200);
}

    //Delete Tasks
     public function destroy($id)
    {
    try {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found'
            ], 404);
        }

        $projectId = $task->project_id;

        // Find the project associated with the task
        $project = Project::find($projectId);

        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'Project not found for the task'
            ], 404);
        }

        // Decrement total_task_created for the associated project
        $project->total_task_created -= 1;
        $project->save();

        // Delete the task
        $task->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Task deleted and total_task_created decremented'
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to delete task: ' . $e->getMessage()
        ], 500);
    }
}

}
