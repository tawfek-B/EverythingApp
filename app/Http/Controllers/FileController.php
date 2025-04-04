<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Lecture;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use Exception;

class FileController extends Controller
{
    public function show360($id)
    {
        // Path to the file in the public directory
        $filePath = public_path(Lecture::findOrFail($id)->file_360);

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
    public function show720($id)
    {
        // Path to the file in the public directory
        $filePath = public_path(Lecture::findOrFail($id)->file_720);
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
    public function show1080($id)
    {
        // Path to the file in the public directory
        $filePath = public_path(Lecture::findOrFail($id)->file_1080);
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
    public function encryptAndGenerateUrl($videoId, $quality)
{
    // if(!Auth::user()->)
    // Retrieve video path from database
    $video = Lecture::findOrFail($videoId);

    // Validate quality parameter
    $qualityMap = [
        0 => 'file_360',
        1 => 'file_720',
        2 => 'file_1080'
    ];

    if (!isset($qualityMap[$quality]) || empty($video->{$qualityMap[$quality]})) {
        return response()->json([
            "success" => false,
            "reason" => $qualityMap[$quality] ? "Quality not available" : "Invalid quality parameter"
        ]);
    }

    $filePath = public_path($video->{$qualityMap[$quality]});

    if (!file_exists($filePath)) {
        return response()->json([
            "success" => false,
            "reason" => "Video file not found"
        ], 404);
    }

    // Ensure encrypted_videos directory exists
    $encryptedDir = public_path('app/encrypted_videos');
    if (!file_exists($encryptedDir)) {
        if (!mkdir($encryptedDir, 0755, true)) {
            return response()->json([
                "success" => false,
                "reason" => "Could not create encryption directory"
            ], 500);
        }
    }

    // Generate unique encrypted filename
    $encryptedFileName = 'encrypted_videos/' . $videoId . '_' . $quality . '_' . time() . '.enc';
    $encryptedFilePath = public_path('app/' . $encryptedFileName);

    try {
        // Get encryption config
        $key = config('app.key');
        $cipher = config('app.cipher');
        $ivLength = openssl_cipher_iv_length($cipher);

        if ($ivLength === false) {
            throw new Exception('Unsupported cipher algorithm');
        }

        // Generate IV
        $iv = random_bytes($ivLength);

        // Process with streams
        $input = fopen($filePath, 'rb');
        $output = fopen($encryptedFilePath, 'wb');

        if (!$input || !$output) {
            throw new Exception('Could not open file streams');
        }

        // Write IV first
        if (fwrite($output, $iv) !== $ivLength) {
            throw new Exception('Failed to write IV');
        }

        // Encrypt in 8MB chunks
        while (!feof($input)) {
            $chunk = fread($input, 8 * 1024 * 1024);
            if ($chunk === false) {
                throw new Exception('Failed to read chunk');
            }

            $encrypted = openssl_encrypt($chunk, $cipher, $key, OPENSSL_RAW_DATA, $iv);
            if ($encrypted === false || fwrite($output, $encrypted) === false) {
                throw new Exception('Encryption/write failed');
            }
        }

        // Close streams
        fclose($input);
        fclose($output);

        // Generate signed URL
        $signedUrl = URL::temporarySignedRoute(
            'download.encrypted.video',
            now()->addMinutes(60),
            ['file' => $encryptedFileName]
        );

        return response()->json([
            "success" => true,
            "url" => $signedUrl
        ]);

    } catch (Exception $e) {
        // Cleanup on failure
        if (isset($output) && is_resource($output)) {
            fclose($output);
            @unlink($encryptedFilePath);
        }
        if (isset($input) && is_resource($input)) {
            fclose($input);
        }

        return response()->json([
            "success" => false,
            "reason" => "Encryption failed",
            // Only show detailed error in development
            "error" => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}

    // Route to serve the encrypted file
    public function serveEncryptedFile(Request $request)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Unauthorized access');
        }

        $file = $request->query('file');
        if (!Storage::exists($file)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return response()->streamDownload(function () use ($file) {
            echo Storage::get($file);
        }, basename($file));
    }


}
