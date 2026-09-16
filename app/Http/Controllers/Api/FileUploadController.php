<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    /**
     * Handle a direct file upload, bypassing Livewire's upload-file endpoint.
     * This avoids WAF/ModSecurity 403 errors on shared hosting.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:png,webp,jpg,jpeg,svg,mp4,mov,avi,webm|max:20480',
            'folder' => 'nullable|string|max:100',
        ]);

        $file = $request->file('file');
        $folder = $request->input('folder', 'uploads');
        
        // Clean original filename to preserve readable names
        $originalName = $file->getClientOriginalName();
        $filename = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        
        $safeName = \Illuminate\Support\Str::slug($filename) . '-' . time() . '.' . $extension;
        
        $path = $file->storeAs($folder, $safeName, 'public');

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => Storage::url($path),
        ]);
    }
}
