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
        $rules=[
            'project_name' => 'required|string',
            'role_id' => 'required',
            'jobs_id' => 'required',
            'team_id' => 'required',
            'pm_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'project_status' => 'required|in:Ongoing,workingOnIt,Completed',
            'total_task_completed' => 'required|integer',
            'total_task_created' => 'required|integer',
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $roleId = $request->input('role_id');
        $roleId= Role::find($roleId);
        if(!$roleId){
            return response()->json([
                'status' => 'error',
                'message' => 'role not found'
            ], 404);
        }

        $jobId = $request->input('jobs_id');
        $jobId= Job::find($jobId);
        if(!$jobId){
            return response()->json([
                'status' => 'error',
                'message' => 'job not found'
            ], 404);
        }

        $teamId = $request->input('team_id');
        $teamId= Team::find($teamId);
        if(!$teamId){
            return response()->json([
                'status' => 'error',
                'message' => 'team not found'
            ], 404);
        }
        // $clientId = $request->input('client_id');
        // $clientId= Client::find($clientId);
        // if(!$clientId){
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'client not found'
        //     ], 404);
        // }
        $pmId = $request->input('pm_id');
        $pmId= Employees::find($pmId);
        if(!$pmId){
            return response()->json([
                'status' => 'error',
                'message' => 'pm not found'
            ], 404);
        }
        $projects = Project::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $projects
        ], 200);
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
}
