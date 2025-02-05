<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    //
    public function test() {//testing if i can send videos, music files and PDFs
        $path = "image.mp3";
        $filePath = storage_path("app\\public\\$path");
        if (file_exists($filePath)) {
            $mimeType = mime_content_type($filePath);
            return response()->json([$filePath, 'Content-Type' => $mimeType]);
        }
    }
}
