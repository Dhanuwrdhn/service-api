<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

use Illuminate\Support\Facades\Validator;

class RolesController extends Controller
{
    //get all
    public function index()
    {
        $roles = Role::all();
        if (!$roles) {
            return response()->json([
                'status' => 'error',
                'message' => 'role not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => $roles
        ]);
    }
    // get by id
    public function show($id)
    {
        $roles = Role::find($id);
        if (!$roles) {
            return response()->json([
                'status' => 'error',
                'message' => 'role not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $roles
        ]);
    }
    // create
    public function create(Request $request){
        $rules=[
            'role_name' => 'required|in:ADMIN,STAFF',
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
        $roles = Role::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $roles
        ], 200);
    }
//update
    public function update(Request $request, $id){
        $rules=[
            'role_name' => 'in:ADMIN,STAFF',
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

        $roles = Role::find($id);

        if (!$roles) {
            return response()->json([
                'status' => 'error',
                'message' => 'role not found'
            ], 404);
        }

        $roles->fill($data);

        $roles->save();
        return response()->json([
            'status' => 'success',
            'data' => $roles
        ]);
    }
    //delete
    public function destroy($id)
    {
        $roles = Role::find($id);

        if (!$roles){
            return response()->json([
                'status' => 'error',
                'message' => 'role not found'
            ], 404);
        }

        $roles->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'role deleted'
        ]);
    }
}
