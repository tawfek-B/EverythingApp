<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Subject;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function fetchAuth()
    {
        $user = Auth::user();

        $subjects = "";
        $count = $user->subjects->count();
        foreach ($user->subjects as $index => $subject) {
            $subjects .= $subject->name;
            if ($index < $count - 1) {
                $subjects .= " - ";
            }
        }

        $userArray = $user->toArray();
        $userArray['subs'] = $subjects;
        $userArray['lecturesNum'] = $user->lectures->count();

        $response = [
            'success' => true,
            'user' => $userArray
        ];

        return response()->json($response);
    }

    public function fetch($id)
    {
        $found = ($user = User::where('id', $id)->first()) ? true : false;
        return response()->json([
            'success' => $found,
            'User' => $user
        ]);
    }

    public function fetchSubjects()
    {
        return response()->json([
            'success' => "true",
            'subjects' => Auth::user()->subjects
        ]);
    }

    public function fetchLectures()
    {
        return response()->json([
            'success' => "true",
            'lectures' => Auth::user()->lectures
        ]);
    }

    public function fetchSubs()
    {
        $user = Auth::user();
        $subjects = "";
        $count = $user->subjects->count();
        foreach ($user->subjects as $index => $subject) {
            $subjects .= $subject->name;
            if ($index < $count - 1)
                $subjects .= " - ";
        }
        return response()->json([
            'success' => "true",
            'subjects' => $subjects,
            'lectures' => Auth::user()->lectures->count()
        ]);
    }

    public function fetchAll()
    {
        return response()->json([
            'success' => "true",
            'users' => User::all()
        ]);
    }

    public function add(Request $request)
    {

        // $request->merge(['user_number' => '+963'.$request->input('user_number')]);//i'll add the country code in the input later

        // $validator = $request->validate([
        //     'user_name' => [
        //         Rule::unique('users', 'userName'),
        //         Rule::unique('admins', 'userName')
        //     ],

        //     'user_number' => [
        //         Rule::unique('admins', 'number'),
        //         Rule::unique('users', 'number')
        //     ],
        // ]);
        // if(!$validator) {

        //     $request->merge(['user_number' => str_replace('+963', '', $request->input('user_number'))]);
        //     return redirect()->back()->withErrors([
        //         'user_name' => 'Name has already been taken',
        //         'user_number' => 'Number has already been taken',
        //     ]);
        // }
        // $userAttributes = $request->validate([
        //     $userName = $request->input('user_name'),
        //     $number = $request->input('user_number'),
        //     $password = $request->input('user_password')
        // ]);
        // User::create([
        //     'userName' => $userName,
        //     'number' => $number,
        //     'password' => $password,
        // ]);
    }

    public function edit(Request $request, $id)
    {//change
        $validator = $request->validate([
            'user_name' => [
                Rule::unique('users', 'userName')->ignore($id)
            ],
            'user_number' => [
                Rule::unique('users', 'number')->ignore($id),
                Rule::unique('admins', 'number')
            ],
        ]);
        if (!$validator) {

            return redirect()->back()->withErrors([
                'user_name' => 'Name has already been taken',
                'user_number' => 'Number has already been taken',
            ]);
        }
        $user = User::findOrFail($id);
        $subjects = json_decode($request->selected_objects, true);
        $lectures = json_decode($request->selected_lectures, true);
        if ($request->selected_lectures == null)
            $lectures = $user->lectures->pluck('id')->toArray();
        // dd($lectures);
        $user->subjects()->sync($subjects);
        $user->lectures()->sync($lectures);
        // dd($subjects);

        $user->userName = $request->user_name;
        $user->number = $request->user_number;

        $user->save();

        foreach (Subject::all() as $subject) {
            $subject->subscriptions = Subject::withCount('users')->find($subject->id)->users_count;
            $subject->save();
        }

        $data = ['element' => 'user', 'id' => $id, 'name' => $user->userName];
        session(['update_info' => $data]);
        return redirect()->route('update.confirmation')->with('link', '/users');
    }

    public function updateUsername(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userName' => 'unique:users,userName'
        ], [
            'userName.unique' => "Already Used"
        ]);
        if ($validator->fails())
            return response()->json([
                'success' => "false",
                'reason' => "Username Already Taken"
            ]);
        else {
            Auth::user()->userName = $request->input('userName');
            Auth::user()->save();
            return response()->json([
                'success' => "true"
            ]);
        }
    }

    public function updatePassword(Request $request)
    {
        if (Hash::check($request->input('oldPassword'), Auth::user()->password)) {
            if ($request->input('newPassword') != null) {
                Auth::user()->password = Hash::make($request->input('newPassword'));
                Auth::user()->save();
                return response()->json([
                    'success' => 'true',
                ]);
            } else
                return response()->json([
                    'success' => "false",
                    'reason' => 'New Password Is Empty'
                ]);
        } else {
            return response()->json([
                'success' => "false",
                'reason' => "Password Doesn't Match"
            ]);
        }
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $name = $user->userName;
        $user->delete();
        foreach (Subject::all() as $subject) {
            $subject->subscriptions = Subject::withCount('users')->find($subject->id);
            $subject->save();
        }
        $data = ['element' => 'user', 'name' => $name];
        session(['delete_info' => $data]);
        return redirect()->route('delete.confirmation')->with('link', '/users');
    }
}
