<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Lecture;
class FileController extends Controller
{
    public function show($id)
    {
        // Path to the file in the public directory
        $filePath = storage_path('app/public/'.Lecture::findOrFail($id)->file);

        // Check if the file exists
        if (!file_exists($filePath)) {
            // dd($filePath);
            abort(404, 'File not found.');
        }

        // Determine the MIME type of the file
        $mimeType = mime_content_type($filePath);

        // Return the file as a response with the appropriate headers
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
        ]);
    }
}
