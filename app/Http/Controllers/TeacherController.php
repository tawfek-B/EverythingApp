<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    //
    public function fetch($id)
    {

        $teacher = Teacher::with(['universities', 'subjects'])->find($id);

        // Check if the teacher was found
        if ($teacher) {
            // Decode the links JSON column (if it's stored as a string)
            $links = is_string($teacher->links) ? json_decode($teacher->links, true) : $teacher->links;

            $count = $teacher->subjects->count();
            $subjects = "";
            foreach ($teacher->subjects as $index => $subject) {
                $subjects .= $subject->name;
                if ($index < $count - 1)
                    $subjects .= " - ";
            }
            $count = $teacher->universities->count();
            $universities = "";
            foreach ($teacher->universities as $index => $university) {
                $universities .= $university->name;
                if ($index < $count - 1)
                    $universities .= " - ";
            }
            // Build the response
            $response = [
                'teacher' => [
                    'id' => $teacher->id,
                    'name' => $teacher->name,
                    'number' => $teacher->number,
                    'image' => $teacher->image,
                    'facebook' => $links['Facebook'] ?? null,
                    'instagram' => $links['Instagram'] ?? null,
                    'telegram' => $links['Telegram'] ?? null,
                    'youtube' => $links['YouTube'] ?? null,
                    'universities' => $universities,
                    'subjects' => $subjects,
                ],
            ];

            return response()->json($response);
        } else {
            return response()->json([
                'teacher' => 'Not Found',
            ], 404);
        }
    }
    public function fetchSubjects($id)
    {
        $teacher = Teacher::find($id);
        if ($teacher) {
            return response()->json([
                'success' => "true",
                'subjects' => $teacher->subjects
            ]);
        } else {
            return response()->json([
                'success' => "false",
                'reason' => "Teacher Not Found"
            ], 404);
        }
    }
    public function fetchSubjectsNames($id)
    {
        $subjects = "";
        $teacher = Teacher::find($id);
        if ($teacher) {
            $count = $teacher->subjects->count();
            foreach ($teacher->subjects as $index => $subject) {
                $subjects .= $subject->name;
                if ($index < $count - 1)
                    $subjects .= " - ";
            }
            return response()->json([
                'success' => "true",
                'subjects' => $subjects
            ]);
        } else {
            return response()->json([
                'success' => "false",
                'reason' => "Teacher Not Found"
            ], 404);
        }
    }
    public function fetchUnis($id)
    {
        $unis = "";
        $teacher = Teacher::find($id);
        if ($teacher) {
            $count = $teacher->universities->count();
            foreach ($teacher->universities as $index => $uni) {
                $unis .= $uni->name;
                if ($index < $count - 1)
                    $unis .= " - ";
            }
            return response()->json([
                'success' => "true",
                'universities' => $unis
            ]);
        } else {
            return response()->json([
                'success' => "false",
                'reason' => "Teacher Not Found"
            ]);
        }
    }
    public function fetchAll()
    {
        // Build the response
        $response = [];
        foreach (Teacher::all() as $teacher) {
            $unis = "";
            $count = $teacher->universities->count();
            foreach ($teacher->universities as $index => $uni) {
                $unis .= $uni->name;
                if ($index < $count - 1)
                    $unis .= " - ";
            }
            $subs = "";
            $count = $teacher->subjects->count();
            foreach ($teacher->subjects as $index => $sub) {
                $subs .= $sub->name;
                if ($index < $count - 1)
                    $subs .= " - ";
            }
            $links = json_decode($teacher->links, true);
            $teachers[] = [
                    'id' => $teacher->id,
                    'name' => $teacher->name,
                    'number' => $teacher->number,
                    'image' => $teacher->image,
                    'facebook' => $links['Facebook'] ?? null,
                    'instagram' => $links['Instagram'] ?? null,
                    'telegram' => $links['Telegram'] ?? null,
                    'youtube' => $links['YouTube'] ?? null,
                    'universities' => $unis,
                    'subjects' => $subs,
            ];
        }

        return response()->json([
            'success' => true,
            'teachers' => $teachers
        ]); //
    }
    public function add(Request $request)
    {
        $validator = $request->validate([
            'teacher_name' => [
                Rule::unique('admins', 'name')
            ],
            'teacher_user_name' => [
                Rule::unique('admins', 'userName'),
                Rule::unique('users', 'userName')
            ],
            'teacher_number' => [
                Rule::unique('admins', 'number'),
                Rule::unique('users', 'number')
            ],

            'facebook_link' => 'nullable|url',
            'instagram_link' => 'nullable|url',
            'telegram_link' => 'nullable|url',
            'youtube_link' => 'nullable|url',
        ]);
        if (!$validator) {
            return redirect()->back()->withErrors([
                'teacher_name' => 'Name has already been taken',
                'teacher_user_name' => 'User name has already been taken',
                'teacher_number' => 'Number has already been taken',
                'facebook_link' => 'Invalid URL',
                'instagram_link' => 'Invalid URL',
                'telegram_link' => 'Invalid URL',
                'youtube_link' => 'Invalid URL',
            ]);
        }
        if (!is_null($request->file('object_image'))) {
            // Handle new image upload
            $file = $request->file('object_image');
            $directory = 'Images/Admins';
            $filename = uniqid().'.'.$file->getClientOriginalExtension();

            // Ensure directory exists
            if (!file_exists(public_path($directory))) {
                mkdir(public_path($directory), 0755, true);
            }

            // Store the image in public folder
            $file->move(public_path($directory), $filename);
            $path = $directory.'/'.$filename;  // Will be "Images/Admins/filename.jpg"
        } else {
            // Use default image
            $path = "Images/Admins/teacherDefault.png";
        }
        $teacherAttributes = $request->validate([
            $userName = $request->input('teacher_user_name'),
            $name = $request->input('teacher_name'),
            $number = $request->input('teacher_number'),
            $password = $request->input('teacher_password')
        ]);
        $teacher = Teacher::create([
            'userName' => $userName,
            'name' => $name,
            'number' => $number,
            'countryCode' => '+963',
            'password' => Hash::make($password),
            'image' => $path,
        ]);
        $links = [
            'Facebook' => $request->input('facebook_link', ''),
            'Instagram' => $request->input('instagram_link', ''),
            'Telegram' => $request->input('telegram_link', ''),
            'YouTube' => $request->input('youtube_link', ''),
        ];

        // Convert the array to a JSON string
        $linksJson = json_encode($links);
        $teacher->links = $linksJson;
        $teacher->save();
        Admin::create([
            'userName' => $userName,
            'name' => $name,
            'countryCode' => '+963',
            'number' => $number,
            'password' => $password,
            'privileges' => 0,
            'teacher_id' => $teacher->id,
            'image' => $path,
        ]);
        $data = ['element' => 'taecher', 'id' => $teacher->id, 'name' => $teacher->name];
        session(['add_info' => $data]);
        return redirect()->route('add.confirmation')->with('link', '/teachers');
    }

    public function edit(Request $request, $id)
    {

        $validator = $request->validate([
            'teacher_name' => [
                Rule::unique('teachers', 'name')->ignore($id)
            ],
            'teacher_user_name' => [
                Rule::unique('teachers', 'userName')->ignore($id),
                Rule::unique('users', 'userName')
            ],
            'teacher_number' => [
                Rule::unique('admins', 'number')->ignore(Admin::where('teacher_id', Teacher::findOrFail($id)->id)->first()->id),
                Rule::unique('users', 'number')
            ],

            'facebook_link' => 'nullable|url',
            'instagram_link' => 'nullable|url',
            'telegram_link' => 'nullable|url',
            'youtube_link' => 'nullable|url',
        ]);
        if (!$validator) {
            dd('asf');
            return redirect()->back()->withErrors([
                'teacher_name' => 'Name has already been taken',
                'teacher_user_name' => 'User name has already been taken',
                'teacher_number' => 'Number has already been taken',
                'facebook_link' => 'Invalid URL',
                'instagram_link' => 'Invalid URL',
                'telegram_link' => 'Invalid URL',
                'youtube_link' => 'Invalid URL',
            ]);
        }
        $links = [
            'Facebook' => $request->input('facebook_link', ''),
            'Instagram' => $request->input('instagram_link', ''),
            'Telegram' => $request->input('telegram_link', ''),
            'YouTube' => $request->input('youtube_link', ''),
        ];

        // Convert the array to a JSON string
        $linksJson = json_encode($links);
        $teacher = Teacher::findOrFail($id);
        $subjects = json_decode($request->selected_objects, true);
        $teacher->subjects()->sync($subjects);
        $teacher->name = $request->teacher_name;
        $teacher->userName = $request->teacher_user_name;
        $teacher->countryCode = '+963';
        $teacher->number = $request->teacher_number;
        $teacher->links = $linksJson;
        if (!is_null($request->file('object_image'))) {
            // Store new image in public/Images/Teachers
            $file = $request->file('object_image');
            $directory = 'Images/Admins';  // Changed from Admins to Teachers
            $filename = uniqid().'.'.$file->getClientOriginalExtension();

            // Ensure directory exists
            if (!file_exists(public_path($directory))) {
                mkdir(public_path($directory), 0755, true);
            }

            // Store the new image
            $file->move(public_path($directory), $filename);
            $path = $directory.'/'.$filename;  // Will be "Images/Teachers/filename.jpg"

            // Delete old image if it's not the default
            if ($teacher->image != "Images/Admins/teacherDefault.png" && file_exists(public_path($teacher->image))) {
                unlink(public_path($teacher->image));
            }

            $teacher->image = $path;
        }
        $teacher->save();

        $teacher = Admin::where('teacher_id', $teacher->id)->first();
        $teacher->name = $request->teacher_name;
        $teacher->userName = $request->teacher_user_name;
        $teacher->number = $request->teacher_number;
        if (!is_null($request->file('object_image')))
            $teacher->image = $path;
        $teacher->save();
        $data = ['element' => 'teacher', 'id' => $id, 'name' => $teacher->name];
        session(['update_info' => $data]);
        return redirect()->route('update.confirmation')->with('link', '/teachers');
    }
    public function delete($id)
    {
        $teacher = Teacher::findOrFail($id);
        $name = $teacher->name;

        if ($teacher->image != "Images/Admins/teacherDefault.png" && file_exists(public_path($teacher->image))) {
            unlink(public_path($teacher->image));
        }

        $admin = Admin::where('teacher_id', $teacher->id)->first();
        $admin->delete();
        $teacher->delete();
        $data = ['element' => 'teacher', 'name' => $name];
        session(['delete_info' => $data]);
        return redirect()->route('delete.confirmation')->with('link', '/teachers');
    }
}
