<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lecture;
use App\Models\Subject;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LectureController extends Controller
{
    public function fetch($id)
    {
        $lec = Lecture::find($id);
        if ($lec) {
            return response()->json([
                'success' => "true",
                'lecture' => $lec
            ]);
        } else {
            return response()->json([
                'success' => "false",
                'reason' => "Lecture Not Found",
            ]);
        }
    }

    public function fetchFile360($id)
    {
        $lecture = Lecture::find($id);
        if ($lecture) {
            $filePath = public_path($lecture->file_360);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => "false",
                    'reason' => "File Not Found"
                ]);
            }

            $mimeType = mime_content_type($filePath);
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
            ]);
        }
        return response()->json([
            'success' => "false",
            'reason' => "Lecture Not Found"
        ]);
    }

    // Similar changes for fetchFile720 and fetchFile1080
    public function fetchFile720($id)
    {
        $lecture = Lecture::find($id);
        if ($lecture) {
            $filePath = public_path($lecture->file_720);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => "false",
                    'reason' => "File Not Found"
                ]);
            }

            $mimeType = mime_content_type($filePath);
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
            ]);
        }
        return response()->json([
            'success' => "false",
            'reason' => "Lecture Not Found"
        ]);
    }

    public function fetchFile1080($id)
    {
        $lecture = Lecture::find($id);
        if ($lecture) {
            $filePath = public_path($lecture->file_1080);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => "false",
                    'reason' => "File Not Found"
                ]);
            }

            $mimeType = mime_content_type($filePath);
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
            ]);
        }
        return response()->json([
            'success' => "false",
            'reason' => "Lecture Not Found"
        ]);
    }

    public function add(Request $request)
    {
        // $request->validate([
        //     'lecture_file_360' => 'nullable|file|mimetypes:video/*',
        //     'lecture_file_720' => 'nullable|file|mimetypes:video/*',
        //     'lecture_file_1080' => 'nullable|file|mimetypes:video/*',
        // ]);

        // if (!$request->hasAny(['lecture_file_360', 'lecture_file_720', 'lecture_file_1080'])) {
        //     return back()->withErrors(['video' => 'Please upload at least one video file']);
        // }

        // Create video directories if they don't exist in public
        $videoDirs = ['360', '720', '1080'];
        foreach ($videoDirs as $dir) {
            $path = public_path("Files/{$dir}");
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
        }

        $name = $request->input('lecture_name');
        $subject_id = $request->input('subject');

        if ($request->hasFile('object_image')) {
            // Store new image in public/Images/Lectures
            $file = $request->file('object_image');
            $directory = 'Images/Lectures';
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            // Ensure directory exists
            if (!file_exists(public_path($directory))) {
                mkdir(public_path($directory), 0755, true);
            }

            // Store the new image
            $file->move(public_path($directory), $filename);
            $path = $directory . '/' . $filename;  // "Images/Lectures/filename.ext"
        } else {
            // Use default image
            $path = "Images/Lectures/default.png";
        }


        // Handle video uploads (public)
        $filePath360 = null;
        $filePath720 = null;
        $filePath1080 = null;

        if ($request->hasFile('lecture_file_360')) {
            $file360 = $request->file('lecture_file_360');
            $fileName360 = time() . '_360_' . $file360->getClientOriginalName();
            $file360->move(public_path('Files/360'), $fileName360);
            $filePath360 = 'Files/360/' . $fileName360;
        }

        if ($request->hasFile('lecture_file_720')) {
            $file720 = $request->file('lecture_file_720');
            $fileName720 = time() . '_720_' . $file720->getClientOriginalName();
            $file720->move(public_path('Files/720'), $fileName720);
            $filePath720 = 'Files/720/' . $fileName720;
        }

        if ($request->hasFile('lecture_file_1080')) {
            $file1080 = $request->file('lecture_file_1080');
            $fileName1080 = time() . '_1080_' . $file1080->getClientOriginalName();
            $file1080->move(public_path('Files/1080'), $fileName1080);
            $filePath1080 = 'Files/1080/' . $fileName1080;
        }

        $lecture = Lecture::create([
            'name' => $name,
            'image' => $path,
            'file_360' => $filePath360,
            'file_720' => $filePath720,
            'file_1080' => $filePath1080,
            'subject_id' => $subject_id,
        ]);

        Subject::findOrFail($subject_id)->lectures()->attach($lecture->id);

        $data = ['element' => 'product', 'id' => $lecture->id, 'name' => $lecture->name];
        session(['add_info' => $data]);
        return redirect()->route('add.confirmation')->with('link', '/lectures');
    }

    public function edit(Request $request, $id)
    {
        $lecture = Lecture::findOrFail($id);
        $lecture->name = $request->lecture_name;
        $lecture->description = $request->lecture_description;

        if ($request->hasFile('object_image')) {
            // Store new image in public/Images/Lectures
            $file = $request->file('object_image');
            $directory = 'Images/Lectures';
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            // Ensure directory exists
            if (!file_exists(public_path($directory))) {
                mkdir(public_path($directory), 0755, true);
            }

            // Store the new image
            $file->move(public_path($directory), $filename);
            $path = $directory . '/' . $filename;

            // Delete old image if it's not the default
            if ($lecture->image != "Images/Lectures/default.png" && file_exists(public_path($lecture->image))) {
                unlink(public_path($lecture->image));
            }

            $lecture->image = $path;
        }

        // Handle video updates (public)
        if ($request->hasFile('lecture_file_360')) {
            if ($lecture->file_360 && file_exists(public_path($lecture->file_360))) {
                unlink(public_path($lecture->file_360));
            }
            $file360 = $request->file('lecture_file_360');
            $fileName360 = time() . '_360_' . $file360->getClientOriginalName();
            $file360->move(public_path('Files/360'), $fileName360);
            $lecture->file_360 = 'Files/360/' . $fileName360;
        }

        if ($request->hasFile('lecture_file_720')) {
            if ($lecture->file_720 && file_exists(public_path($lecture->file_720))) {
                unlink(public_path($lecture->file_720));
            }
            $file720 = $request->file('lecture_file_720');
            $fileName720 = time() . '_720_' . $file720->getClientOriginalName();
            $file720->move(public_path('Files/720'), $fileName720);
            $lecture->file_720 = 'Files/720/' . $fileName720;
        }

        if ($request->hasFile('lecture_file_1080')) {
            if ($lecture->file_1080 && file_exists(public_path($lecture->file_1080))) {
                unlink(public_path($lecture->file_1080));
            }
            $file1080 = $request->file('lecture_file_1080');
            $fileName1080 = time() . '_1080_' . $file1080->getClientOriginalName();
            $file1080->move(public_path('Files/1080'), $fileName1080);
            $lecture->file_1080 = 'Files/1080/' . $fileName1080;
        }

        $lecture->save();

        $data = ['element' => 'lecture', 'id' => $id, 'name' => $lecture->name];
        session(['update_info' => $data]);
        return redirect()->route('update.confirmation')->with('link', '/lectures');
    }

    public function delete($id)
    {
        $lecture = Lecture::findOrFail($id);
        $name = $lecture->name;

        // Delete old image if it's not the default
        if ($lecture->image != "Images/Lectures/default.png" && file_exists(public_path($lecture->image))) {
            unlink(public_path($lecture->image));
        }

        // Delete videos from public
        if ($lecture->file_360 && file_exists(public_path($lecture->file_360))) {
            unlink(public_path($lecture->file_360));
        }
        if ($lecture->file_720 && file_exists(public_path($lecture->file_720))) {
            unlink(public_path($lecture->file_720));
        }
        if ($lecture->file_1080 && file_exists(public_path($lecture->file_1080))) {
            unlink(public_path($lecture->file_1080));
        }

        $lecture->delete();

        // Update subjects lecture counts
        foreach (Subject::all() as $subject) {
            $subject->lecturesCount = Subject::withCount('lectures')->find($subject->id)->lectures_count;
            $subject->save();
        }

        $data = ['element' => 'lecture', 'name' => $name];
        session(['delete_info' => $data]);
        return redirect()->route('delete.confirmation')->with('link', '/lectures');
    }
}