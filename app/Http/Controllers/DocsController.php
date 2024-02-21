<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocsController extends Controller
{
     public function store(Request $request)
    {
        $request->validate([
            'team_id' => 'sometimes|exists:mg_teams,id',
            'role_id' => 'sometimes|exists:mg_roles,id',
            'jobs_id' => 'sometimes|exists:mg_jobs,id',
            'project_id' => 'sometimes|exists:mg_projects,id',
            'document_name' => 'string',
            'document_desc' => 'string',
            'creator_id' => 'required|exists:mg_employee,id',
            'document_file' => 'required|file|mimes:png,jpg,pdf,doc,docx',
        ]);

        $documentFile = $request->file('document_file');
        $documentFileName = time() . '_' . $documentFile->getClientOriginalName();
        $documentFile->storeAs('public/documents', $documentFileName);

        $document = Document::create([
            'team_id' => $request->team_id,
            'role_id' => $request->role_id,
            'jobs_id' => $request->jobs_id,
            'project_id' => $request->project_id,
            'document_name' => $request->document_name,
            'document_desc' => $request->document_desc,
            'creator_id' => $request->creator_id,
            'document_file' => $documentFileName,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Document uploaded successfully',
            'document' => $document
        ], 200);
    }
}
