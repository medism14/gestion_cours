<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Display a listing of documents.
     * Filtered by user role visibility.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $loup = null;

        // Base query with visibility filter
        $query = Document::visibleTo($user)->active()->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->input('search')) {
            $search = $request->input('search');
            $loup = 667;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('filename', 'like', '%' . $search . '%');
            });
        }

        $documents = $query->paginate(10);

        return view('authed.documents', [
            'documents' => $documents,
            'loup' => $loup
        ]);
    }

    /**
     * Store a newly uploaded document.
     * Admin only.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'addTitle' => 'required|string|max:255',
            'addDescription' => 'nullable|string',
            'addVisibility' => 'required|in:all,teachers,students',
            'addFile' => 'required|file|mimes:pdf|max:51200', // 50 Mo max
        ], [
            'addTitle.required' => 'Le titre est requis.',
            'addFile.required' => 'Le fichier PDF est requis.',
            'addFile.mimes' => 'Seuls les fichiers PDF sont acceptés.',
            'addFile.max' => 'Le fichier ne doit pas dépasser 50 Mo.',
        ]);

        if ($validator->fails()) {
            $errors = json_decode($validator->errors(), true);
            return redirect()->back()->with(['error' => $errors]);
        }

        $file = $request->file('addFile');
        $originalName = $file->getClientOriginalName();
        $fileName = time() . '_' . $originalName;
        $filePath = $file->storeAs('documents', $fileName, 'public');
        $fileSize = $file->getSize();
        $fileType = $file->getClientOriginalExtension();

        Document::create([
            'title' => $request->input('addTitle'),
            'description' => $request->input('addDescription'),
            'filename' => $originalName,
            'path' => $filePath,
            'filetype' => $fileType,
            'file_size' => $fileSize,
            'visibility' => $request->input('addVisibility'),
            'is_active' => true,
            'user_id' => auth()->user()->id,
        ]);

        return redirect()->back()->with(['success' => 'Le document a bien été ajouté.']);
    }

    /**
     * Upload a chunk of a large file.
     * Used for files > 10 Mo.
     */
    public function uploadChunk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chunk' => 'required|file',
            'chunkIndex' => 'required|integer|min:0',
            'totalChunks' => 'required|integer|min:1',
            'uploadId' => 'required|string',
            'filename' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $chunk = $request->file('chunk');
        $chunkIndex = $request->input('chunkIndex');
        $uploadId = $request->input('uploadId');

        // Store chunk in temporary directory
        $chunkPath = "chunks/{$uploadId}";
        $chunk->storeAs($chunkPath, "chunk_{$chunkIndex}", 'local');

        return response()->json([
            'success' => true,
            'chunkIndex' => $chunkIndex,
            'message' => "Chunk {$chunkIndex} uploaded successfully"
        ]);
    }

    /**
     * Finalize chunked upload by assembling all chunks.
     */
    public function finalizeUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uploadId' => 'required|string',
            'filename' => 'required|string',
            'totalChunks' => 'required|integer|min:1',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'visibility' => 'required|in:all,teachers,students',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $uploadId = $request->input('uploadId');
        $filename = $request->input('filename');
        $totalChunks = $request->input('totalChunks');
        $chunkPath = storage_path("app/chunks/{$uploadId}");

        // Create final file
        $finalFileName = time() . '_' . $filename;
        $finalPath = storage_path("app/public/documents/{$finalFileName}");

        // Ensure documents directory exists
        if (!file_exists(storage_path('app/public/documents'))) {
            mkdir(storage_path('app/public/documents'), 0755, true);
        }

        // Assemble chunks
        $finalFile = fopen($finalPath, 'wb');
        
        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkFile = "{$chunkPath}/chunk_{$i}";
            if (file_exists($chunkFile)) {
                $chunkContent = file_get_contents($chunkFile);
                fwrite($finalFile, $chunkContent);
                unlink($chunkFile); // Delete chunk after use
            } else {
                fclose($finalFile);
                return response()->json(['error' => "Chunk {$i} missing"], 400);
            }
        }
        
        fclose($finalFile);

        // Remove chunk directory
        if (is_dir($chunkPath)) {
            rmdir($chunkPath);
        }

        // Get file size
        $fileSize = filesize($finalPath);

        // Create document record
        $document = Document::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'filename' => $filename,
            'path' => "documents/{$finalFileName}",
            'filetype' => pathinfo($filename, PATHINFO_EXTENSION),
            'file_size' => $fileSize,
            'visibility' => $request->input('visibility'),
            'is_active' => true,
            'user_id' => auth()->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'document' => $document,
            'message' => 'Document uploadé avec succès'
        ]);
    }

    /**
     * Get document details.
     */
    public function getDocument($id)
    {
        $document = Document::with('user')->find($id);

        if (!$document) {
            return response()->json(['error' => 'Document introuvable'], 404);
        }

        return response()->json($document);
    }

    /**
     * Download a document.
     */
    public function download($id)
    {
        $document = Document::find($id);

        if (!$document) {
            return redirect()->back()->with(['error' => 'Document introuvable']);
        }

        // Check visibility
        $user = auth()->user();
        if ($user->role == 1 && $document->visibility == 'students') {
            return redirect()->back()->with(['error' => 'Accès non autorisé']);
        }
        if ($user->role == 2 && $document->visibility == 'teachers') {
            return redirect()->back()->with(['error' => 'Accès non autorisé']);
        }

        $filePath = storage_path("app/public/{$document->path}");

        if (!file_exists($filePath)) {
            return redirect()->back()->with(['error' => 'Fichier introuvable sur le serveur']);
        }

        return response()->download($filePath, $document->filename);
    }

    /**
     * Update document metadata.
     * Admin only.
     */
    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:documents,id',
            'editTitle' => 'required|string|max:255',
            'editDescription' => 'nullable|string',
            'editVisibility' => 'required|in:all,teachers,students',
            'editIsActive' => 'nullable|boolean',
        ], [
            'editTitle.required' => 'Le titre est requis.',
        ]);

        if ($validator->fails()) {
            $errors = json_decode($validator->errors(), true);
            return redirect()->back()->with(['error' => $errors]);
        }

        $document = Document::find($request->input('id'));
        $document->title = $request->input('editTitle');
        $document->description = $request->input('editDescription');
        $document->visibility = $request->input('editVisibility');
        $document->is_active = $request->has('editIsActive') ? $request->input('editIsActive') : $document->is_active;

        // Handle file replacement if new file provided
        if ($request->hasFile('editFile')) {
            $validator = Validator::make($request->all(), [
                'editFile' => 'file|mimes:pdf|max:51200',
            ], [
                'editFile.mimes' => 'Seuls les fichiers PDF sont acceptés.',
                'editFile.max' => 'Le fichier ne doit pas dépasser 50 Mo.',
            ]);

            if ($validator->fails()) {
                $errors = json_decode($validator->errors(), true);
                return redirect()->back()->with(['error' => $errors]);
            }

            // Delete old file
            $oldPath = storage_path("app/public/{$document->path}");
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }

            // Upload new file
            $file = $request->file('editFile');
            $originalName = $file->getClientOriginalName();
            $fileName = time() . '_' . $originalName;
            $filePath = $file->storeAs('documents', $fileName, 'public');

            $document->filename = $originalName;
            $document->path = $filePath;
            $document->file_size = $file->getSize();
            $document->filetype = $file->getClientOriginalExtension();
        }

        $document->save();

        return redirect()->back()->with(['success' => 'Le document a bien été modifié.']);
    }

    /**
     * Delete a document.
     * Admin only.
     */
    public function delete($id)
    {
        $document = Document::find($id);

        if (!$document) {
            return redirect()->back()->with(['error' => 'Document introuvable']);
        }

        // Delete file from storage
        $filePath = storage_path("app/public/{$document->path}");
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $document->delete();

        return redirect()->back()->with(['success' => 'Le document a bien été supprimé.']);
    }
}
