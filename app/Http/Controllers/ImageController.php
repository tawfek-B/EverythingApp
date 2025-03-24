<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\university;
use App\Models\Lecture;
use App\Models\Subject;

class ImageController extends Controller
{
    //
    public function fetchTeacher($id)
    {
        $teacher = Teacher::find($id);
        if ($teacher) {
            $path = $teacher->image;
            $filePath = storage_path("app\\public\\$path");
            if (file_exists($filePath)) {
                $mimeType = mime_content_type($filePath);
                return response()->file($filePath, ['Content-Type' => $mimeType]);
            }
            return response()->json([
                'success' => 'false',
                'reason' => 'Image Not Found'
            ], 404);
        } else {
            return response()->json([
                'success' => 'false',
                'reason' => 'Teacher Not Found'
            ], 404);
        }
    }
    public function fetchLecture($id)
    {
        $lecture = Lecture::find($id);
        if ($lecture) {
            $path = $lecture->image;
            $filePath = storage_path("app\\public\\$path");
            if (file_exists($filePath)) {
                $mimeType = mime_content_type($filePath);
                return response()->file($filePath, ['Content-Type' => $mimeType]);
            }
            return response()->json([
                'success' => 'false',
                'reason' => 'Image Not Found'
            ], 404);
        } else {
            return response()->json([
                'success' => 'false',
                'reason' => 'Lecture Not Found'
            ], 404);
        }
    }
    public function fetchSubject($id)
    {
        $subject = Subject::find($id);
        if ($subject) {
            $path = $subject->image;
            $filePath = storage_path("app\\public\\$path");
            if (file_exists($filePath)) {
                $mimeType = mime_content_type($filePath);
                return response()->file($filePath, ['Content-Type' => $mimeType]);
            }
            return response()->json([
                'success' => 'false',
                'reason' => 'Image Not Found'
            ], 404);
        } else {
            return response()->json([
                'success' => 'false',
                'reason' => 'Subject Not Found'
            ], 404);
        }
    }

    public function fetchUniversity($id)
    {
        $uni = university::find($id);
        if ($uni) {
            $path = $uni->image;
            $filePath = storage_path("app\\public\\$path");
            if (file_exists($filePath)) {
                $mimeType = mime_content_type($filePath);
                return response()->file($filePath, ['Content-Type' => $mimeType]);
            }
            return response()->json([
                'success' => 'false',
                'reason' => 'Image Not Found'
            ], 404);
        } else {
            return response()->json([
                'success' => 'false',
                'reason' => 'University Not Found'
            ], 404);
        }
    }

}