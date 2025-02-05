<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Teacher;
use App\Models\Subject;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    //
    public function add(Request $request)
    {
        if ($request->input('subject') == null) {
            $validator = Validator::make($request->all(), [
                'name' => 'unique:teachers,name',
                'number' => 'unique:teachers,number',
                'userName' => 'unique:teachers,userName',
                'password' => 'min:8'
            ], [
                'name.unique' => 'Already Used',
                'number.unique' => 'Already Used',
                'userName.unique' => 'Already Used',
                'password' => 'Password not long enough',
            ]);
            if ($validator->fails()) {
                // Return all validation errors
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }
            $subjectID = $request->input('subjectID');
        }
        else {
            $validator = Validator::make($request->all(), [
                'name' => 'unique:teachers,name',
                'number' => 'unique:teachers,number',
                'userName' => 'unique:teachers,userName',
                'password' => 'min:8',
                'subject' => 'unique:subjects,name'
            ], [
                'name.unique' => 'Already Used',
                'number.unique' => 'Already Used',
                'userName.unique' => 'Already Used',
                'password' => 'Password not long enough',
                'subject.unique' => 'Already Used'
            ]);
            if ($validator->fails()) {
                // Return all validation errors
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }
            $subject = Subject::make(['name' => $request->input('subject')]);
            $subject->save();
            $subjectID = $subject->id;
        }

        $teacherAttributes = $request->validate([
            'name' => ['required'],
            'number' => ['required'],
            'userName' => ['required'],
        ]);
        $teacher = Teacher::make($teacherAttributes);
        $teacher->password = Hash::make($request->input('password'));
        $teacher->save();
        if($request->input('subject')!=null) {
            $subject->teacher_id += $teacher->id;
            $subject->save();
        }
        return response()->json([
            'Success' =>'true',
            'Teacher' => $teacher
        ]);//Have to change it so if there is more than one teacher per subject.
    }

    public function edit(Request $request) {

    }
}
