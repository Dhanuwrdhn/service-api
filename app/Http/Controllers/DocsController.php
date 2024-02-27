<?php

namespace App\Http\Controllers;

use App\Models\Docs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DocsController extends Controller
{
        public function getById($id)
    {
        $document = Docs::find($id);

        if (!$document) {
            return response()->json([
                'status' => 'error',
                'message' => 'Document not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'document' => $document
        ], 200);
    }

    public function getAll()
    {
        $documents = Docs::all();

        return response()->json([
            'status' => 'success',
            'documents' => $documents
        ], 200);
    }

     use Illuminate\Support\Facades\DB;

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
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
            // Simpan file di dalam direktori 'public/documents'
            $documentFile->storeAs('public/documents', $documentFileName);

            // Path file yang disimpan
            $documentFilePath = 'documents/' . $documentFileName;

            $document = Docs::create([
                'team_id' => $request->team_id,
                'role_id' => $request->role_id,
                'jobs_id' => $request->jobs_id,
                'project_id' => $request->project_id,
                'document_name' => $request->document_name,
                'document_desc' => $request->document_desc,
                'creator_id' => $request->creator_id,
                'document_file' => $documentFileName,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Document uploaded successfully',
                'document' => $document
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to upload document',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $document = Docs::find($id);

            if (!$document) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Document not found'
                ], 404);
            }

            $request->validate([
                'team_id' => 'sometimes|exists:mg_teams,id',
                'role_id' => 'sometimes|exists:mg_roles,id',
                'jobs_id' => 'sometimes|exists:mg_jobs,id',
                'project_id' => 'sometimes|exists:mg_projects,id',
                'document_name' => 'string',
                'document_desc' => 'string',
                'creator_id' => 'sometimes|exists:mg_employee,id',
                'document_file' => 'sometimes|file|mimes:png,jpg,pdf,doc,docx',
            ]);

            if ($request->hasFile('document_file')) {
                $documentFile = $request->file('document_file');
                $documentFileName = time() . '_' . $documentFile->getClientOriginalName();
                $documentFile->storeAs('public/documents', $documentFileName);
                $document->document_file = $documentFileName;
            }

            $document->team_id = $request->input('team_id', $document->team_id);
            $document->role_id = $request->input('role_id', $document->role_id);
            $document->jobs_id = $request->input('jobs_id', $document->jobs_id);
            $document->project_id = $request->input('project_id', $document->project_id);
            $document->document_name = $request->input('document_name', $document->document_name);
            $document->document_desc = $request->input('document_desc', $document->document_desc);
            $document->creator_id = $request->input('creator_id', $document->creator_id);

            $document->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Document updated successfully',
                'document' => $document
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update document',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $document = Docs::find($id);

            if (!$document) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Document not found'
                ], 404);
            }

            // Hapus file dokumen dari storage jika ada
            if (Storage::exists('public/documents/' . $document->document_file)) {
                Storage::delete('public/documents/' . $document->document_file);
            }

            $document->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Document deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete document',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
