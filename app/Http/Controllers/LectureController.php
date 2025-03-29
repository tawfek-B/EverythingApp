<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lecture;
use App\Models\Subject;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LectureController extends Controller
{
    //
    // public function test($id)
    // {//testing if i can send videos, music files and PDFs
    //     $path = Lecture::findOrFail($id)->file;
    //     $filePath = storage_path("app\\public\\$path");
    //     if (file_exists($filePath)) {
    //         return response()->file($filePath);
    //     }
    // }

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

            $filePath = storage_path('app/public/' . Lecture::findOrFail($id)->file_360);

            // Check if the file exists
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => "false",
                    'reason' => "File Not Found"
                ]);
            }

            // Determine the MIME type of the file
            $mimeType = mime_content_type($filePath);

            // Return the file as a response with the appropriate headers
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
            ]);
        } else {
            return response()->json([
                'success' => "false",
                'reason' => "Lecture Not Found"
            ]);
        }
    }
    public function fetchFile720($id)
    {

        $lecture = Lecture::find($id);
        if ($lecture) {

            $filePath = storage_path('app/public/' . Lecture::findOrFail($id)->file_720);

            // Check if the file exists
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => "false",
                    'reason' => "File Not Found"
                ]);
            }

            // Determine the MIME type of the file
            $mimeType = mime_content_type($filePath);

            // Return the file as a response with the appropriate headers
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
            ]);
        } else {
            return response()->json([
                'success' => "false",
                'reason' => "Lecture Not Found"
            ]);
        }
    }
    public function fetchFile1080($id)
    {

        $lecture = Lecture::find($id);
        if ($lecture) {

            $filePath = storage_path('app/public/' . Lecture::findOrFail($id)->file_1080);

            // Check if the file exists
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => "false",
                    'reason' => "File Not Found"
                ]);
            }

            // Determine the MIME type of the file
            $mimeType = mime_content_type($filePath);

            // Return the file as a response with the appropriate headers
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
            ]);
        } else {
            return response()->json([
                'success' => "false",
                'reason' => "Lecture Not Found"
            ]);
        }
    }

    public function add(Request $request)
    {
        $request->validate([
            'lecture_file_360' => 'nullable|file|mimetypes:video/*',
            'lecture_file_720' => 'nullable|file|mimetypes:video/*',
            'lecture_file_1080' => 'nullable|file|mimetypes:video/*',
        ], [
            'required_without_all' => 'Please upload at least one video file',
        ]);

        // Custom validation to ensure at least one file is uploaded
        if (
            !$request->hasFile('lecture_file_360') &&
            !$request->hasFile('lecture_file_720') &&
            !$request->hasFile('lecture_file_1080')
        ) {
            return back()->withErrors([
                'video' => 'Please upload at least one video file'
            ]);
        }
        $name = $request->input('lecture_name');
        // $description = $request->input('lecture_description');
        $file360 = $request->file('lecture_file_360');
        $file720 = $request->file('lecture_file_720');
        $file1080 = $request->file('lecture_file_1080');
        $image = $request->file('object_image');
        $subject_id = $request->input('subject');

        if (!is_null($request->file('object_image'))) {
            $path = $image->store('Lectures', 'public');
        } else {
            $path = "Lectures/default.png";
        }
        if ($file360 != null)
            $filePath360 = $file360->store('Files/360', 'public');
        if ($file720 != null)
            $filePath720 = $file720->store('Files/720', 'public');
        if ($file1080 != null)
            $filePath1080 = $file1080->store('Files/1080', 'public');
        $lecture = Lecture::create([
            'name' => $name,
            // 'description' => $description,
            'image' => $path,
            'file_360' => $filePath360 ?? null,
            'file_720' => $filePath720 ?? null,
            'file_1080' => $filePath1080 ?? null,
            'subject_id' => $subject_id,
        ]);
        Subject::findOrFail($request->input('subject'))->lectures()->attach($lecture->id);
        $lecture->save();
        $data = ['element' => 'product', 'id' => $lecture->id, 'name' => $lecture->name];
        session(['add_info' => $data]);
        return redirect()->route('add.confirmation')->with('link', '/lectures');
    }

    public function edit(Request $request, $id)
    {
        // dd($request->all());
        $lecture = Lecture::findOrFail($id);
        $lecture->name = $request->lecture_name;
        $lecture->description = $request->lecture_description;
        if (!is_null($request->file('object_image'))) {
            $path = $request->file('object_image')->store('Lectures', 'public');
            if ($lecture->image != "Lectures/default.png") {
                Storage::disk('public')->delete($lecture->image);
            }
            $lecture->image = str_replace('public\\', '', $path);//this replaces what's already in the user logo for the recently stored new pic
        }
        if (!is_null($request->file('lecture_file_360'))) {
            $filePath360 = $request->file('lecture_file_360')->store('Files/360', 'public');
            $lecture->file_360 = $filePath360;
        }
        if (!is_null($request->file('lecture_file_720'))) {
            $filePath720 = $request->file('lecture_file_720')->store('Files/720', 'public');
            $lecture->file_720 = $filePath720;
        }
        if (!is_null($request->file('lecture_file_1080'))) {
            $filePath1080 = $request->file('lecture_file_1080')->store('Files/1080', 'public');
            $lecture->file_1080 = $filePath1080;
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
        if ($lecture->image != "Lectures/default.png")
            Storage::disk('public')->delete($lecture->image);
        Storage::disk('public')->delete($lecture->file_360);
        Storage::disk('public')->delete($lecture->file_720);
        Storage::disk('public')->delete($lecture->file_1080);
        $lecture->delete();


        foreach (Subject::all() as $subject) {
            $subject->lecturesCount = Subject::withCount('lectures')->find($subject->id)->lectures_count;
            $subject->save();
        }
        $data = ['element' => 'lecture', 'name' => $name];
        session(['delete_info' => $data]);
        return redirect()->route('delete.confirmation')->with('link', '/lectures');
    }
}
