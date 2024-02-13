<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Job;
use Illuminate\Support\Facades\Validator;

class JobsController extends Controller
{
    //get all
    public function index()
    {
        $jobs = Job::all();
        return response()->json([
            'status' => 'success',
            'data' => $jobs
        ]);
    }
    // get by id
    public function show($id)
    {
        $jobs = Job::find($id);
        if (!$jobs) {
            return response()->json([
                'status' => 'error',
                'message' => 'jobs not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $jobs
        ]);
    }
    // create
    public function create(Request $request){
        $rules=[
            'job_name' => 'required|String|unique:mg_jobs,job_name',
            'description' => 'String',
        ];
        $data = $request->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }
        $jobs = Job::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $jobs
        ], 200);
    }
//update
    public function update(Request $request, $id){
        $rules=[
            'job_name' => 'String',
            'description' => 'String',
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $jobs = Job::find($id);

        if (!$jobs) {
            return response()->json([
                'status' => 'error',
                'message' => 'jobs not found'
            ], 404);
        }

        $jobs->fill($data);

        $jobs->save();
        return response()->json([
            'status' => 'success',
            'data' => $jobs
        ]);
    }
    //delete
    public function destroy($id)
    {
        $jobs = Job::find($id);

        if (!$jobs){
            return response()->json([
                'status' => 'error',
                'message' => 'jobs not found'
            ], 404);
        }

        $jobs->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'jobs deleted'
        ]);
    }
}
