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
        // get all employee who assigned to task from table employeeTask
        foreach ($tasks as $task) {
            // $assignedEmployees = EmployeeTasks::where('tasks_id', $task->id)->get('id');
            $assignedEmployees = EmployeeTasks::with(['employee' => function($query) {
                $query->select('id', 'employee_name');
            }])->where('tasks_id', $task->id)->get();
            $task->assignedEmployees = $assignedEmployees->pluck('employee');

        }

        return response()->json([
            'status' => 'success',
            'data' => $tasks
        ]);
    }

    public function showTaskSpecific($taskid)
    {
        $task = Task::where('id', $taskid)->get();
        $assignedEmployees = EmployeeTasks::with(['employee' => function($query) {
            $query->select('id', 'employee_name');
        }])->where('tasks_id', $taskid)->get();
        $task->assignedEmployees = $assignedEmployees->pluck('employee');

        if ($task->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $task
        ]);
    }


    //Create Tasks
    public function createTask(Request $request)
    {
        // Aturan validasi untuk input
        $rules = [
            'project_id' => 'required|exists:mg_projects,id', // Pastikan project_id adalah ID proyek yang valid
            'assign_by' => 'required|exists:mg_employee,id', // Pastikan assign_by adalah ID karyawan yang valid
            'task_name' => 'required|string',
            'task_desc' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'percentage_task' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if ($value % 5 !== 0 || $value == 0) {
                        $fail($attribute.' cannot be 0 and must be a multiple of 5.');
                    }
                },
            ],
            'task_status' => 'in:onPending,onReview,workingOnIt,Completed',// Pastikan task_status di antara nilai yang valid
            'task_image' => 'string|nullable',
            'task_reason' => 'string|nullable',
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
        Validator::extend('not_assigned', function ($attribute, $value, $parameters, $validator) use ($request) {
            //Validate to check if employee already assigned with the same subtask name
            $task = Task::join('mg_employee_tasks', 'mg_tasks.id', '=', 'mg_employee_tasks.tasks_id')
                                ->where('mg_tasks.task_name', $request->input('task_name'))
                                ->where('mg_employee_tasks.employee_id', $value)
                                ->exists();

            if ($task) {
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
                    'message' => 'Project and PM not found'
                ], 404);
            }
            //mengambil semua subtask dan mengambil jumlah persentase dan jumlahkan seluruhnya
            $inputtedPercentage = $request->input('percentage_task');
            $totalTaskPercentage = Task::where('project_id', $request->input('project_id'))->sum('percentage_task');

            if($totalTaskPercentage + $inputtedPercentage > 100){
                return response()->json([
                    'status' => 'error',
                    'total will be' => $totalTaskPercentage + $inputtedPercentage,
                    'message' => 'Total percentage of subtask is more than 100%'
                ], 400);
            }
            
            // Membuat tugas baru
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
                'data' => $task,
                'assigned_to' => $assignedToIds,
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
            'percentage_task' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if ($value % 5 !== 0 || $value == 0) {
                        $fail($attribute.' cannot be 0 and must be a multiple of 5.');
                    }
                },
            ],
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


            //edit assigned to
            $assignedToIds = $request->input('assign_to');
            // delete employeetask where task_id = $id
            EmployeeTasks::where('tasks_id', $id)->delete();
            // add new employee task
            foreach ($assignedToIds as $assignedToId) {
                EmployeeTasks::create([
                    'tasks_id' => $task->id,
                    'project_id' => $task->project_id,
                    'employee_id' => $assignedToId,
                ]);
            }
            $task->update($validator->validated());

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'task updated successfully',
                'data' => $task, 
                'assigned_to' => $assignedToIds,
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to edit task: ' . $e->getMessage()
            ], 500);
        }
    }

    // accept task
    public function acceptTask(Request $request, $id){
        try{
            DB::beginTransaction();

            $task = Task::findOrFail($id);
            //error handling before accept task
            if($task->task_status === 'workingOnIt'){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Task already Accepted'
                ], 400);
            }else if($task->task_status === 'onReview'){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Task already asking for review'
                ], 400);
            }
            $employeeTask = EmployeeTasks::where('tasks_id', $id)
                            ->where('employee_id', $request->input('employee_id'))
                            ->first();
            if(!$employeeTask){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee not found or assigned to this task'
                ], 400);
            }
            if($employeeTask->isAccepted === true){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Task already accepted'
                ], 400);
            }
            //accept task
            $employeeTask->update([
                'isAccepted' => true
            ]);
            $task->update([
                'task_status' => 'workingOnIt'
            ]);
            
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Task accepted'
            ]);
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to accept task: ' . $e->getMessage()
            ], 500);
        }
    }

    // reject task
    public function rejectTask(Request $request, $id){
        try{
            DB::beginTransaction();

            $task = Task::findOrFail($id);
            //error handling before reject task
            $employeeTask = EmployeeTasks::where('tasks_id', $id)
                            ->where('employee_id', $request->input('employee_id'))
                            ->first();
            if(!$employeeTask){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee not assigned to this task'
                ], 400);
            }
            if($employeeTask->isAccepted){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Task already accepted'
                ], 400);
            }
            if($task->task_status == 'onReview'){
                return response()->json([
                    'status' => 'error',
                    'message' => 'cannot reject task, because task is already asking for review'
                ], 400);
            }
            //reject task
            $employeeTask->update([
                'isAccepted' => null,
            ]);
            $task->update([
                'task_status' => 'onReview',
                'task_reason' => $request->input('task_reason'),
            ]);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Task rejected'
            ]);
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reject task: ' . $e->getMessage()
            ], 500);
        }
    }

    // submit task
    public function submitTask(Request $request, $id){
        //validator
        $rules = [
            'task_image' => 'required|string',
            'task_reason' => 'sometimes|string',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        try{
            DB::beginTransaction();

            // check if task is already submitted
            $task = Task::findOrFail($id);
            if($task->task_status == 'onReview'){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Task already submitted, waiting for review'
                ], 400);
            }


            // check status of when task is completed
            $currDate = new \DateTime();
            $endDate = new \DateTime($task->end_date);
            $daysDifference = $currDate->format('d') - $endDate->format('d');

            if ($daysDifference < 0) {
                $taskStatus = 'earlyFinish';
            } elseif ($daysDifference === 0) {
                $taskStatus = 'finish'; 
            } elseif ($daysDifference <= 3) {
                $taskStatus = 'finish in delay'; 
            } else {
                $taskStatus = 'overdue';
            }


            // Decode data URI base64 to image file
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->input('task_image')));
            $imageName = uniqid() . '.png';
            $imagePath = 'photos/' . $imageName;
            $path = public_path($imagePath);
            // handle resubmit with image
            if($task->task_image){
                $oldImagePath = public_path($task->task_image);
                if(file_exists($oldImagePath)){
                    unlink($oldImagePath);
                }
            }
            // Save image to storage
            file_put_contents($path, $imageData);
            $task->update([
                'task_status' => 'onReview',
                'task_image' => $imagePath,
                'task_reason' => $request->input('task_reason'),
                'task_submit_status' => $taskStatus,
            ]);
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Task submitted successfully',
                'data' => $task
            ]);
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to submit task: ' . $e->getMessage()
            ], 500);
        }
    }

    // delete task
    public function deleteTask($id){
        try{
            DB::beginTransaction();

            $task = Task::findOrFail($id);
            $project = Project::findOrFail($task->project_id);
            $project->total_task_created -= 1;
            $project->save();
            $task->delete();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Task deleted and total task in project decreased by 1'
            ]);
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete task: ' . $e->getMessage()
            ], 500);
        }
    }

    //review task
    public function reviewTask(Request $request, $id){
        try{
            DB::beginTransaction();
            $task = Task::findOrFail($id);
            //error handling before review task
            if($task->task_status === 'Completed'){return response()->json(['status' => 'error','message' => 'Task already completed'], 400);}
            elseif($task->task_status === 'onPending'){return response()->json(['status' => 'error','message' => 'unable to review because status is already reviewed and currently on pending'], 400);}
            elseif($task->task_status === 'workingOnIt'){return response()->json(['status' => 'error','message' => 'unable to review because status is already reviewed and currently on working'], 400);}
            elseif($task->task_status != 'onReview' && $task->task_image === null){
                return response()->json(['status' => 'error','message' => 'unable to review because status is already reviewed, please edit instead'], 400);
            }
            
            //validate request
            $validatedData = $request->validate([
                'task_name' => 'sometimes|string',
                'task_desc' => 'sometimes|string',
                'start_date' => 'sometimes|date',
                'end_date' => 'sometimes|date',
                'percentage_task' => 'sometimes|in:0,5,10,15,20,25,30,35,40,45,50,55,60,65,70,75,80,85,90,95,100',
                // 'task_status' => 'sometimes|in:onPending,onReview,workingOnIt,Completed',
                'assign_to' => 'sometimes|array', // assign_to harus berupa array
                'assign_to.*' => [ // setiap item di assign_to harus ada dalam mg_employee_project dan terkait dengan proyek yang sesuai
                    'exists:mg_employee_project,employee_id',
                    function ($attribute, $value, $fail) use ($request,$id) {
                        // Validasi tambahan: pastikan karyawan terkait dengan proyek yang sesuai
                        $projectId = Task::where('id', $id)->first('project_id')->project_id;
                        $employeeProject = EmployeeProject::where('employee_id', $value)
                                                        ->where('project_id', $projectId)
                                                        ->exists();
                        if (!$employeeProject) {
                            $fail("Employee with ID $value is not associated with the specified project.");
                        }
                    },
                ],
                'isAccepted' => 'sometimes|boolean', //to check if review reject or review submission task
            ]);

            //check if task is review for submission or reject
            if ($task->task_image === null || $task->task_image === 0){
                $validatedData['task_status'] = 'onPending';
                $validatedData['task_reason']=null;
                $task->update($validatedData);
                //update assignTo
                $assignedToIds = $request->input('assign_to');
                // delete employeetask where task_id = $id
                EmployeeTasks::where('tasks_id', $id)->delete();
                // add new employee task
                foreach ($assignedToIds as $assignedToId) {
                    EmployeeTasks::create([
                        'tasks_id' => $task->id,
                        'project_id' => $task->project_id,
                        'employee_id' => $assignedToId,
                    ]);
                }
                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Rejected task is reviewed successfully',
                    'data' => $task,
                    'assigned_to' => $assignedToIds,
                ]);
            }
            //review for submission
            if($request->input('isAccepted')){
                $validatedData['task_status'] = 'Completed';
                $validatedData['task_reason'] = $request->input('task_reason')?? $task['task_reason'];
                $task->update($validatedData);
                //update project status
                $updatedProject = Project::findOrFail($task->project_id);
                $totalPercentageOfCompletedTask = Task::where('project_id', $task->project_id)->where('task_status', 'Completed')->sum('percentage_task');
                //jika semisal gak mau manual update status project
                    // if($totalPercentageOfCompletedTask == 100){
                    //     $updatedProject->project_status = 'Completed';
                    // }
                $updatedProject->percentage= $totalPercentageOfCompletedTask;
                $updatedProject->total_task_completed += 1;
                $updatedProject->save();

                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Submitted Task is reviewed successfully',
                    'data' => $task
                ]);
            } else if(!$request->input('isAccepted')){
                $validatedData['task_status'] = 'workingOnIt';
                $validatedData['task_reason'] = $request->input('task_reason')?? $task['task_reason'];
                $task->update($validatedData);
                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Submitted task is rejected successfully, and will be working on it',
                    'data' => $task
                ]);
            }
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to review task: ' . $e->getMessage()
            ], 500);
        }
    }


}
