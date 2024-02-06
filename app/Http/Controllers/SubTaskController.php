<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\SubTasks;
use Illuminate\Support\Facades\Validator;

class SubTaskController extends Controller
{
    public function index(Request $request){
    $subtasks = SubTasks::all();
    if(!$subtasks){
        return response()->json([
            'status' => 'error',
            'message' => 'subtask not found'
        ], 404);
    }
    return response()->json([
        'status' => 'success',
        'data' => $subtasks
    ]);
    }
    public function showSubTask($id){
        $subtask= SubTasks::find($id);
        if(!$subtask){
            return response()->json([
                'status' => 'error',
                'message' => 'subtask not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => $subtask
        ]);
    }
    public function createSubTask(Request $request){
        $rules=[
            'task_id' => 'required',
            'subtask_name' => 'required',
            'subtask_description' => 'String',
            'start_date' => 'required',
            'end_date' => 'required',
            'subtask_status' => 'in:onPending,onReview,workingOnIt,Completed',
            'subtask_submit_status' => 'in:earlyFinish,finish,finish in delay,overdue',
            'subtask_percentage' => 'required',
            'subtask_image' => 'string',
        ];
        $data = $request->all();
        $validator =
        Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }
        $tasksId=$request->input('task_id');
        $tasks = Task::find($tasksId);
        if(!$tasks){
            return response()->json([
                'status' => 'error',
                'message' => 'task not found'
            ], 404);
        }
        $subtask = SubTasks::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $subtask
        ]);

    }
}
