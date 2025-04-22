<?php

namespace App\Http\Controllers;

use App\Models\MedicalFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MedicalFileController extends Controller
{
    /**
     * Display a listing of the medical files.
     */
    public function index()
    {
        $this->authorize('viewAny', MedicalFile::class);
        
        $files = MedicalFile::with(['encounter.patient'])->paginate(15);
        
        return inertia('MedicalFiles/Index', [
            'files' => $files
        ]);
    }

    /**
     * Store a newly created medical file in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', MedicalFile::class);
        
        $validated = $request->validate([
            'encounter_id' => 'required|exists:encounters,id',
            'name' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // 10MB max file size
            'description' => 'nullable|string'
        ]);
        
        $path = $request->file('file')->store('medical-files');
        
        $file = MedicalFile::create([
            'encounter_id' => $validated['encounter_id'],
            'name' => $validated['name'],
            'path' => $path,
            'description' => $validated['description'] ?? null,
            'file_type' => $request->file('file')->getMimeType(),
            'file_size' => $request->file('file')->getSize(),
            'uploaded_by' => Auth::id()
        ]);
        
        return redirect()->back()->with('success', 'File uploaded successfully.');
    }

    /**
     * Display the specified medical file.
     */
    public function show(MedicalFile $file)
    {
        $this->authorize('view', $file);
        
        return inertia('MedicalFiles/Show', [
            'file' => $file->load(['encounter.patient'])
        ]);
    }

    /**
     * Download a medical file.
     */
    public function download(MedicalFile $file)
    {
        $this->authorize('view', $file);
        
        if (!Storage::exists($file->path)) {
            abort(404, 'File not found.');
        }
        
        return Storage::download(
            $file->path, 
            $file->name . '.' . pathinfo($file->path, PATHINFO_EXTENSION)
        );
    }

    /**
     * Remove the specified medical file from storage.
     */
    public function destroy(MedicalFile $file)
    {
        $this->authorize('delete', $file);
        
        if (Storage::exists($file->path)) {
            Storage::delete($file->path);
        }
        
        $file->delete();
        
        return redirect()->route('medical-files.index')->with('success', 'File deleted successfully.');
    }
}