<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PatientUploadController extends Controller
{
    // List uploads (JSON)
    public function index(Request $request)
    {
        $patientId = $request->query('patient_id');
        if (!$patientId) {
            return response()->json(['success' => false, 'message' => 'patient_id required'], 400);
        }

        $files = PatientUpload::where('patient_id', $patientId)->get()->map(function ($f) {
            return [
                'id' => $f->id,
                'file_path' => $f->file_path,
                'original_name' => $f->original_name,
                'file_size' => $f->file_size,
                'url' => route('patient.uploads.download', $f->id),
                'created_at' => $f->created_at,
            ];
        });

        return response()->json(['success' => true, 'files' => $files]);
    }

    // Store upload
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB
            'patient_id' => 'required|integer',
        ]);

        $file = $request->file('file');
        $patientId = $request->input('patient_id');

        $originalName = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $filename = Str::random(12) . '_' . preg_replace('/[^A-Za-z0-9\.\-\_]/', '_', $originalName);
        // Store on the local disk (storage/app) to keep files private
        $path = $file->storeAs('patient_uploads/' . $patientId, $filename, 'local');

        $upload = PatientUpload::create([
            'patient_id' => $patientId,
            'file_path' => $path,
            'original_name' => $originalName,
            'file_size' => $fileSize,
        ]);

        return response()->json([
            'success' => true,
            'file' => [
                'id' => $upload->id,
                'url' => route('patient.uploads.download', $upload->id),
                'file_path' => $upload->file_path,
                'original_name' => $upload->original_name,
                'file_size' => $upload->file_size,
            ]
        ]);
    }

    // Delete upload
    public function destroy(PatientUpload $upload)
    {
        // Delete file from storage
        if ($upload->file_path && Storage::disk('local')->exists($upload->file_path)) {
            Storage::disk('local')->delete($upload->file_path);
        }
        $upload->delete();

        return response()->json(['success' => true]);
    }

    // View upload (inline display)
    public function view(PatientUpload $upload)
    {
        if (!$upload->file_path || !Storage::disk('local')->exists($upload->file_path)) {
            abort(404);
        }

        // Get the full file path using the local disk
        $fullPath = Storage::disk('local')->path($upload->file_path);

        if (!file_exists($fullPath)) {
            abort(404);
        }

        // Get file extension and mime type
        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
        $mimeType = $this->getMimeType($extension);

        // Return file with appropriate headers for inline viewing
        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline'
        ]);
    }

    // Helper method to get mime type based on extension
    private function getMimeType($extension)
    {
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'webp' => 'image/webp',
        ];

        return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }

    // Download upload
    public function download(PatientUpload $upload)
    {
        if (!$upload->file_path || !Storage::disk('local')->exists($upload->file_path)) {
            abort(404);
        }

        // Get the full file path using the local disk
        $fullPath = Storage::disk('local')->path($upload->file_path);

        if (!file_exists($fullPath)) {
            abort(404);
        }

        // Use the original filename for download, fallback to extracted name if not available
        $downloadName = $upload->original_name;
        if (!$downloadName) {
            // Fallback: extract from file_path by removing the random prefix
            $storedName = basename($upload->file_path);
            if (preg_match('/^[a-zA-Z0-9]{12}_(.+)$/', $storedName, $matches)) {
                $downloadName = $matches[1];
            } else {
                $downloadName = $storedName;
            }
        }

        return response()->download($fullPath, $downloadName);
    }
}
