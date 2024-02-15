<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\SubTasks;
use App\Models\Project;
use App\Models\Employees;
use App\Models\EmployeeTasks;
use App\Models\EmployeeSubtasks;
use App\Models\EmployeeProject;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SubTaskController extends Controller
{
    // public function index(Request $request){
    // $subtasks = SubTasks::all();
    // if(!$subtasks){
    //     return response()->json([
    //         'status' => 'error',
    //         'message' => 'subtask not found'
    //     ], 404);
    // }
    // return response()->json([
    //     'status' => 'success',
    //     'data' => $subtasks
    // ]);
    // }
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

    public function createSubTasks(Request $request){

        // Aturan validasi untuk input
        $rules = [
            'subtask_name' => 'required',
            'task_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'assign_by' => 'required|exists:mg_employee,id',
            'assign_to' => 'required|array', // assign_to harus berupa array
            'assign_to.*' => [ // each item in assign_to must exist in mg_employee_project and be associated with the appropriate project
                'exists:mg_employee_tasks,employee_id',
                function ($attribute, $value, $fail) use ($request) {
                    $task = Task::find($request->input('task_id'));

                    $employeeTask = EmployeeTasks::where('employee_id', $value)
                                                ->where('tasks_id', $task->id)
                                                ->exists();

                    if (!$employeeTask) {
                        $fail("Employee with ID $value is not associated with the specified Tasks.");
                    }
                }
            ],
            'subtask_status' => 'in:onPending,onReview,workingOnIt,Completed',
            'subtask_submit_status' => 'in:earlyFinish,finish,finish in delay,overdue',
            'subtask_percentage' => 'required|string',
            'subtask_image' => 'string|nullable',
            'subtask_description' => 'string|nullable',
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

    try {
        // Memulai transaksi database
        DB::beginTransaction();
         // Mendapatkan proyek dan karyawan yang terlibat
        $task = Task::find($request->input('task_id'));

        $assignBy = Employees::find($request->input('assign_by')); // Ubah ke Employee

        // Membuat subtask
        $subtask = SubTasks::create($request->all());
        // Pastikan proyek dan karyawan yang terlibat ditemukan
        if (!$task || !$assignBy) {
            throw new \Exception('task or assignBy not found.');
        }

        // Membuat tugas
        // $task = Task::create($request->all());

        // Mengassign tugas kepada karyawan
        $assignedToIds = $request->input('assign_to');
        foreach ($assignedToIds as $assignedToId) {
            EmployeeSubtasks::create([
                'employee_id' => $assignedToId,
                'tasks_id' => $task->id,
                'subtasks_id' => $subtask->id,
            ]);
        }
        // Menambahkan jumlah tugas yang dibuat ke dalam proyek
        $task->total_subtask_created += 1;
        $task->save();

        // Commit transaksi database
        DB::commit();

        // Respon berhasil
        return response()->json([
            'status' => 'success creating subtask',
            'data' => $subtask
        ], 200);
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

        // Decode data URI base64 ke dalam binary data gambar
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->confirmation_image));

        // Simpan data gambar ke dalam file
        $imageName = uniqid() . '.png'; // Generate nama unik untuk gambar
        $imagePath = 'photos\\' . $imageName; // Path baru untuk menyimpan di public/photos
        $path = public_path($imagePath); // Path lengkap ke direktori public
        file_put_contents($path, $imageData);

        // Simpan path gambar konfirmasi dalam basis data
        $subtask->update([
            'subtask_status' => $request->subtask_status,
            'subtask_submit_status' => $subtaskSubmitStatus,
            'subtask_image' => $imagePath, // Simpan path gambar, bukan data biner
        ]);

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



    // show all subtasks by task
    public function showSubTasksByTask($task_id){
        try{
            $subtasks = SubTasks::where('task_id', $task_id)
                                ->get();

            if(!$subtasks){
                return response()->json([
                    'status' => 'error',
                    'message' => 'subtask not found'
                ], 404);
            }
            return response()->json([
                'status' => 'success',
                'data' => $subtasks
            ],200);

        }catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to show all subtask: ' . $e->getMessage()
            ], 500);
        }
    }

    //show all subtasks by employeeid

    public function showSubTasksByEmployee(){
        try{
            //get token from header
            //get employee_id from token
            // $subtasks = SubTasks::where('employee_id', $employee_id)
            //                     ->get();

            // if(!$subtasks){
            //     return response()->json([
            //         'status' => 'error',
            //         'message' => 'subtask not found'
            //     ], 404);
            // }
            return response()->json([
                'status' => 'not yet implemented',
            ],200);

        }catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to show all subtask: ' . $e->getMessage()
            ], 500);
        }
    }



}
