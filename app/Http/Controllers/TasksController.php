<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;

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
    public function create(Request $request)
    {
        // Add code here to create tasks
        $rules = [
            'project_id' => 'required',
            'task_name' => 'required',
            'task_description' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'assigned_by' => 'required',
            'assigned_to' => 'required',
            'percentage_task' => 'string',
            'total_subtask_completed' => 'string',
            'task_status' => 'required',
        ];
        $data = $request->all();
        $validator = Validator::make($data, $rules);

        if ($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }
        $projectId = $request->input('project_id');
        $project= Project::find($projectId);
        if(!$project){
            return response()->json([
                'status' => 'error',
                'message' => 'project not found'
            ], 404);
        }
        $project->save();
        $tasks = Task::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $tasks
        ], 200);
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
