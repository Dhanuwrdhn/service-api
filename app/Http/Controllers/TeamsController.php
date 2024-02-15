<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Team;
use Illuminate\Support\Facades\Validator;

class TeamsController extends Controller
{
    //get all
    public function index()
    {
        $teams = Team::all();
        return response()->json([
            'status' => 'success',
            'data' => $teams
        ]);
    }
    // get by id
    public function show($id)
    {
        $teams = Team::find($id);
        if (!$teams) {
            return response()->json([
                'status' => 'error',
                'message' => 'team not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $teams
        ]);
    }
    // create
    public function create(Request $request){
        $rules=[
            'team_name' => 'required|String',
            'description' => 'nullable|String',
        ];
        $data = $request->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }
        $teams = Team::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $teams
        ], 200);
    }
//update
    public function update(Request $request, $id){
        $rules=[
            'team_name' => 'String',
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

        $teams = Team::find($id);

        if (!$teams) {
            return response()->json([
                'status' => 'error',
                'message' => 'team not found'
            ], 404);
        }

        $teams->fill($data);

        $teams->save();
        return response()->json([
            'status' => 'success',
            'data' => $teams
        ]);
    }
    //delete
    public function destroy($id)
    {
        $teams = Team::find($id);

        if (!$teams){
            return response()->json([
                'status' => 'error',
                'message' => 'team not found'
            ], 404);
        }

        $teams->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'team deleted'
        ]);
    }
}
