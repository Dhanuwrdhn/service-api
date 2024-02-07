<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\Employees;
use App\Models\EmployeeTasks;

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
    //Create Tasks
        public function createTask(Request $request)
{
    $rules = [
        'task_name' => 'required|string',
        'task_description' => 'nullable|string',
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'assign_by' => 'required|exists:mg_employee,id',
        'percentage_task' => 'nullable|string',
        'total_subtask_completed' => 'nullable|string',
        'task_status' => 'required|in:onPending,onReview,workingOnIt,Completed', // Perbaiki sintaks in
    ];
    $data = $request->all();
    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => $validator->errors()
        ], 400);
    }

    try {
        DB::beginTransaction();

        // Retrieve project and employee
        $assignBy = Employees::find($request->input('assign_by')); // Ubah ke Employee

        // Ensure project and employee exist
        if ( !$assignBy) {
            throw new \Exception('Project or assignBy not found.');
        }

        // Create the task
        $task = Task::create($data, $rules);

        // Assign the task to employees
        $project = Project::find($request->input('project_id'));
        $assignedToIds = $request->input('assign_to');
        foreach ($assignedToIds as $assignedToId) {
            EmployeeTasks::create([
                'tasks_id' => $task->id,
                'project_id' => $project->id,
                'employee_id' => $assignedToId,
            ]);
        }

        DB::commit();

        return response()->json([
            'status' => 'success',
            'data' => $task
        ], 200);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to create task. ' . $e->getMessage()
        ], 500);
    }
}

    //Update status Tasks
    public function updateStatus(Request $request, $id){
        $rules = [
            'task_status' => 'required',
        ];
        $data = $request->only('task_status');
        $task = Task::find($id);
        $validator = Validator::make($data, $rules);

        if ($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $task->update($data);
        // Check if the task status is Completed, then update mg_projects
        if ($data['task_status'] == 'Completed') {
            $project = Project::find($task->project_id);

            if ($project) {
                $project->increment('total_task_completed');
                // Jika Anda juga ingin mengupdate kolom lain, sesuaikan di sini
                // $project->update(['total_task_completed' => $project->total_task_completed + 1]);
            }

        return response()->json([
            'status' => 'success',
            'data' => $task
        ], 200);
    }
    //Delete Tasks
    //Show Tasks

    }
}
