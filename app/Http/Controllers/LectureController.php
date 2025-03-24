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
    public function fetchFile($id)
    {

        $lecture = Lecture::find($id);
        if ($lecture) {

            $filePath = storage_path('app/public/' . Lecture::findOrFail($id)->file);

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
        $name = $request->input('lecture_name');
        $description = $request->input('lecture_description');
        $file = $request->file('lecture_file');
        $image = $request->file('object_image');
        $subject_id = $request->input('subject');

        if (!is_null($request->file('object_image'))) {
            $path = $image->store('Lectures', 'public');
        } else {
            $path = "Lectures/default.png";
        }
        $filePath = $file->store('Files', 'public');
        $lecture = Lecture::create([
            'name' => $name,
            'description' => $description,
            'image' => $path,
            'file' => $filePath,
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
        $lecture->save();
        $data = ['element' => 'lecture', 'id' => $id, 'name' => $lecture->name];
        session(['update_info' => $data]);
        return redirect()->route('update.confirmation')->with('link', '/lectures');


    }
    public function delete($id)
    {
        $lecture = Lecture::findOrFail($id);
        $name = $lecture->name;
        Storage::disk('public')->delete($lecture->image);
        Storage::disk('public')->delete($lecture->file);
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
