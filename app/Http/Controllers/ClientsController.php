<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Validator;

class ClientsController extends Controller
{
    //get all
    public function index()
    {
        $clients = Client::all();
        return response()->json([
            'status' => 'success',
            'data' => $clients
        ]);
    }
    // get by id
    public function show($id)
    {
        $clients = Client::find($id);
        if (!$clients) {
            return response()->json([
                'status' => 'error',
                'message' => 'clients not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $clients
        ]);
    }
    // create
    public function create(Request $request){
        $rules=[
            'client_name' => 'required|String',
            'client_type' => 'string',
            'client_contact' => 'string',
            'client_address' => 'string',
        ];
        $data = $request->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }
        $clients = Client::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $clients
        ], 200);
    }
//update
    public function update(Request $request, $id){
        $rules=[
            'client_name' => 'required|String',
            'client_type' => 'string',
            'client_contact' => 'string',
            'client_address' => 'string',
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $clients = Client::find($id);

        if (!$clients) {
            return response()->json([
                'status' => 'error',
                'message' => 'clients not found'
            ], 404);
        }

        $clients->fill($data);

        $clients->save();
        return response()->json([
            'status' => 'success',
            'data' => $clients
        ]);
    }
    //delete
    public function destroy($id)
    {
        $clients = Client::find($id);

        if (!$clients){
            return response()->json([
                'status' => 'error',
                'message' => 'clients not found'
            ], 404);
        }

        $clients->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'clients deleted'
        ]);
    }
}
