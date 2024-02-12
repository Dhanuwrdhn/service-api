<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\SubTasks;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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
// submitSUbtask
    public function submitSubtask(Request $request, $id){

        $rules = [
        'subtask_status' => 'required|in:Completed',
        'confirmation_image' => 'required|string', // Ubah validasi gambar menjadi string
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

        $subtask = SubTasks::findOrFail($id);

        // Mendapatkan tanggal sekarang
        $currentDate = now();

        // Mendapatkan tanggal berakhir subtask
        $endDate = $subtask->end_date;

        // Menghitung selisih hari antara tanggal sekarang dan tanggal berakhir subtask
        $daysDifference = $currentDate->diffInDays($endDate, false);

        // Menentukan subtask_submit_status berdasarkan selisih hari
        if ($daysDifference < 0) {
            $subtaskSubmitStatus = 'earlyFinish'; // Jika selesai sebelum end_date
        } elseif ($daysDifference == 0) {
            $subtaskSubmitStatus = 'finish'; // Jika selesai tepat pada end_date
        } elseif ($daysDifference <= 3) {
            $subtaskSubmitStatus = 'finish in delay'; // Jika selesai kurang dari atau sama dengan 3 hari setelah end_date
        } else {
            $subtaskSubmitStatus = 'overdue'; // Jika melewati 3 hari setelah end_date
        }

        $subtask->update([
            'subtask_status' => $request->subtask_status,
            'subtask_submit_status' => $subtaskSubmitStatus,
        ]);

        // Simpan gambar konfirmasi dalam basis64 ke dalam file
        $imageData = base64_decode($request->confirmation_image);
        $imageName = uniqid() . '.png'; // Generate nama unik untuk gambar
        $imagePath = 'confirmation_images/' . $imageName;
        file_put_contents($imagePath, $imageData);

        // Simpan path gambar konfirmasi dalam basis data
        $subtask->update(['subtask_image' => $imagePath]);

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'Subtask submitted successfully',
            'data' => $subtask
        ]);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to submit subtask: ' . $e->getMessage()
        ], 500);
    }
}


}
