<?php

namespace App\Http\Controllers;

use App\Models\Docs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

        if ($documents->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No documents found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'documents' => $documents
        ], 200);
    }
        public function downloadDocument($id)
    {
        try {
            $document = Docs::findOrFail($id);

            // Dapatkan path lengkap dari file
            $filePath = storage_path('app/public/' . $document->document_file);

            // Periksa apakah file ada
            if (!Storage::exists('public/' . $document->document_file)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File not found'
                ], 404);
            }

            // Dapatkan nama file asli
            $originalFileName = basename($filePath);

            // Buat respons untuk mengunduh file
            return response()->download($filePath, $originalFileName);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to download document',
                'error' => $e->getMessage()
            ], 500);
        }
}
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

            // Ambil tanggal saat ini dan ubah formatnya menjadi tanggal yang sesuai
            $currentDate = now()->format('d_F_Y'); // Misalnya, 21_February_2024

            // Ubah nama file dengan menambahkan tanggal ke depannya
            $documentFileName = $currentDate . '_' . $documentFile->getClientOriginalName();

            // Simpan file di dalam direktori 'public/documents'
            $documentFile->storeAs('documents', $documentFileName, 'public');

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
                'document_file' => $documentFilePath, // Gunakan path file relatif
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



    public function updateDocument(Request $request, $id)
{
    DB::beginTransaction();

    try {
        // Temukan dokumen berdasarkan ID

        $document = Docs::find($id);

        // Jika dokumen tidak ditemukan, kembalikan respons dengan status 404
        if (!$document) {
            return response()->json([
                'status' => 'error',
                'message' => 'Document not found'
            ], 404);
        }

        // Validasi request menggunakan Validator
        $validator = Validator::make($request->all(), [
            'document_file' => 'required|file|mimes:png,jpg,pdf,doc,docx',
        ]);

        // Jika validasi gagal, kembalikan respons dengan pesan kesalahan validasi
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()->all()
            ], 422); // 422 Unprocessable Entity
        }

        // Update atribut document_file jika ada file baru yang diunggah
        if (!$request->hasFile('document_file')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Document file not found'
            ]);
        }
        $newDocumentFile = $request->file('document_file');

        // Hapus file dokumen lama dari penyimpanan
        if (Storage::exists('public/documents/' . $document->document_file)) {
            Storage::delete('public/documents/' . $document->document_file);
        }

        // Ambil tanggal saat ini dan ubah formatnya menjadi tanggal yang sesuai
        $currentDate = now()->format('d_F_Y');

        // Ubah nama file dengan menambahkan tanggal ke depannya
        $newDocumentFileName = $currentDate . '_' . $newDocumentFile->getClientOriginalName();

        // Simpan file di dalam direktori 'public/documents'
        $newDocumentFile->storeAs('public/documents', $newDocumentFileName);

        // Update informasi file dokumen
        $document->document_file = $newDocumentFileName;

        // Simpan perubahan pada dokumen
        $document->save();

        DB::commit();

        // Tambahkan header ke respons
        return response()->json([
            'status' => 'success',
            'message' => 'Document updated successfully',
            'document' => $document
        ], 200)->header('Custom-Header', $document);
    } catch (\Exception $e) {
        DB::rollback();

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update document',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function deleteDocs($id)
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
