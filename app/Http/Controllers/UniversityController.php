<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\university;
use App\Models\Teacher;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UniversityController extends Controller
{
    //

    public function fetch($id)
    {
        $uni = university::find($id);
        if ($uni) {
            return response()->json([
                'success' => "true",
                'university' => $uni,
            ]);
        } else {
            return response()->json([
                'success' => "false",
                'reason' => "University Not Found"
            ]);
        }
    }

    public function fetchall()
    {
        return response()->json([
            'universities' => university::count() ? university::all() : null,
        ]);
    }

    public function fetchTeachers($id)
    {
        $uni = University::find($id);

        if (!$uni) {
            return response()->json([
                'success' => false, // Changed to boolean
                'reason' => "University Not Found"
            ], 404);
        }

        $teachers = $uni->teachers->map(function ($teacher) {
            // Decode the links JSON
            $links = json_decode($teacher->links, true) ?? [];

            return [
                // Keep existing fields
                'id' => $teacher->id,
                'name' => $teacher->name,
                'userName' => $teacher->userName,
                'countryCode' => $teacher->countryCode,
                'number' => $teacher->number,
                'image' => $teacher->image,

                // Add the new social media fields
                'Facebook' => $links['Facebook'] ?? null,
                'Telegram' => $links['Telegram'] ?? null,
                'YouTube' => $links['YouTube'] ?? null,

                // Remove these:
                // 'password' => $teacher->password, // Excluded for security
                // 'links' => $teacher->links,      // Excluded since we're breaking it down
            ];
        });

        return response()->json([
            'success' => true,
            'teachers' => $teachers->count() ? $teachers : null,
        ]);
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'university_name' => 'unique:universities,name'
        ], [
            'university_name.unique' => "Already Used"
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors(['university_name' => "Name Has Already Been Taken"])->withInput(["university_name"]);
        }

        if (!is_null($request->file('object_image'))) {
            // Store new image in public/Images/Universities
            $file = $request->file('object_image');
            $directory = 'Images/Universities';
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            // Ensure directory exists (create if needed)
            if (!file_exists(public_path($directory))) {
                mkdir(public_path($directory), 0755, true);
            }

            // Store the new image
            $file->move(public_path($directory), $filename);
            $path = $directory . '/' . $filename;  // "Images/Universities/filename.ext"
        } else {
            // Use default image
            $path = "Images/Universities/default.png";
        }

        $uni = university::make(['name' => $request->input('university_name')]);
        $uni->image = $path;
        $uni->save();
        $data = ['element' => 'univerisity', 'id' => $uni->id, 'name' => $uni->name];
        session(['add_info' => $data]);
        return redirect()->route('add.confirmation')->with('link', '/universities');
    }

    public function edit(Request $request, $id)
    {
        // dd($request->all());
        $uniAttributes = $request->validate([
            'university_name' => [
                Rule::unique('universities', 'name')->ignore($id),
            ],
        ]);
        if (!$uniAttributes) {
            return redirect()->back()->withErrors([
                'university_name' => 'Name has alread been taken.'
            ]);
        }
        $uni = university::findOrFail($id);
        $teachers = json_decode($request->selected_objects, true);
        $uni->teachers()->sync($teachers);
        if (!is_null($request->file('object_image'))) {
            // Store new image in public/Images/Universities
            $file = $request->file('object_image');
            $directory = 'Images/Universities';
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            // Ensure directory exists
            if (!file_exists(public_path($directory))) {
                mkdir(public_path($directory), 0755, true);
            }

            // Store the new image
            $file->move(public_path($directory), $filename);
            $path = $directory . '/' . $filename;

            // Delete old image if it's not the default
            if ($uni->image != "Images/Universities/default.png" && file_exists(public_path($uni->image))) {
                unlink(public_path($uni->image));
            }

            $uni->image = $path;
        }
        $uni->name = $request->input('university_name');
        $uni->save();
        $data = ['element' => 'university', 'id' => $id, 'name' => $uni->name];
        session(['update_info' => $data]);
        return redirect()->route('update.confirmation')->with('link', '/universities');
    }

    public function delete($id)
    {
        $uni = university::findOrFail($id);
        $name = $uni->name;

        // Delete old image if it's not the default
        if ($uni->image != "Images/Universities/default.png" && file_exists(public_path($uni->image))) {
            unlink(public_path($uni->image));
        }

        $uni->delete();

        $data = ['element' => 'university', 'name' => $name];
        session(['delete_info' => $data]);
        return redirect()->route('delete.confirmation')->with('link', '/universities');
    }
}
