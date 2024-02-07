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
    use Illuminate\Support\Facades\DB;

public function createSubTask(Request $request)
{
    $rules = [
        'task_id' => 'required',
        'subtask_name' => 'required',
        'subtask_description' => 'string|nullable',
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'subtask_status' => 'in:onPending,onReview,workingOnIt,Completed',
        'subtask_submit_status' => 'in:earlyFinish,finish,finish in delay,overdue',
        'subtask_percentage' => 'required|string',
        'subtask_image' => 'string|nullable',
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

        $tasksId = $request->input('task_id');
        $tasks = Task::find($tasksId);

        if (!$tasks) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found'
            ], 404);
        }

        $subtask = SubTask::create($validator->validated());

        DB::commit();

        return response()->json([
            'status' => 'success',
            'data' => $subtask
        ]);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to create subtask: ' . $e->getMessage()
        ], 500);
    }
}

}
