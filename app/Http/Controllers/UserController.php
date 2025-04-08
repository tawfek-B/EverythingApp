<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Subject;
use App\Models\Lecture;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

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
        $user->isBanned = $request->isBanned == "on" ? 1 : 0;

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

    // public function confirmSubSub($id)
    // {
    //     return response()->json([
    //         'success' => true,
    //         'isSubscribed' => Auth::user()->subjects->pluck('id')->contains($id),
    //     ]);
    // }

    public function editCounter(Request $request)
    {

        $isBanned = false;

        $isLoggedOut = false;

        $user = Auth::user();
        $now = now();
        $lastScreenshotTime = $user->last_screenshot_at;
        // Reset counter if more than 30 minutes have passed since last screenshot
        if ($lastScreenshotTime && Carbon::parse($lastScreenshotTime)->diffInMinutes($now) >= 5) {
            $user->counter = 1;
            $user->last_screenshot_at = Carbon::now();
            $user->save();
        } else {
            $user->increment('counter');
            $user->last_screenshot_at = Carbon::now();
            // dd(Carbon::now());
        }

        if ($user->counter > 1 && $user->counter < 4) {

            Auth::user()->remember_token = null;
            Auth::user()->save();
            Auth::user()->currentAccessToken()->delete();

            $isBanned = false;
            $isLoggedOut = true;
        }
        if ($user->counter >= 4) {

            $user->counter = 0;

            $user->isBanned = true;
            $user->save();

            $user->remember_token = null;
            $user->save();
            $user->currentAccessToken()->delete();

            $isBanned = true;
            $isLoggedOut = true;
        }

        if (!$lastScreenshotTime || $user->counter === 1) {
            $user->update(attributes: ['last_screenshot_at' => Carbon::now()]);
        }


        $user->save();
        return response()->json([
            'success' => true,
            'counter' => $isBanned ? 4 :$user->counter,
            'isLoggedOut' => $isLoggedOut,
            'isBanned' => $isBanned
        ]);
    }

    public function confirmLecSub($id)
    {
        if (is_null(Lecture::find($id))) {
            return response()->json([
                'success' => false,
                'reason' => "Lecture Not Found"
            ], 404);
        } else {
            return response()->json([
                'success' => true,
                'isSubscribed' => Auth::user()->lectures->pluck('id')->contains($id) || Auth::user()->subjects->pluck('id')->contains(Lecture::findOrFail($id)->subject_id),
            ]);
        }
    }



    // public function confirmSubSub($id) {
    //     try {
    //         if (!Auth::check()) {
    //             return response()->json([
    //                 'error' => 'Unauthenticated',
    //                 'message' => 'User not logged in'
    //             ], 401);
    //         }

    //         if (!is_numeric($id)) {
    //             return response()->json([
    //                 'error' => 'Invalid input',
    //                 'message' => 'Subject ID must be numeric'
    //             ], 422);
    //         }

    //         $user = Auth::user();

    //         if (!$user->relationLoaded('subjects')) {
    //             $user->load('subjects');
    //         }

    //         return response()->json([
    //             'isSubscribed' => $user->subjects->pluck('id')->contains((int)$id),
    //             'subject_id' => $id
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => 'Server error',
    //             'message' => 'An error occurred while checking subscription',
    //             'details' => config('app.debug') ? $e->getMessage() : null
    //         ], 500);
    //     }
    // }

    // public function confirmLecSub($id) {
    //     try {
    //         if (!Auth::check()) {
    //             return response()->json([
    //                 'error' => 'Unauthenticated',
    //                 'message' => 'User not logged in'
    //             ], 401);
    //         }

    //         if (!is_numeric($id)) {
    //             return response()->json([
    //                 'error' => 'Invalid input',
    //                 'message' => 'Lecture ID must be numeric'
    //             ], 422);
    //         }

    //         $user = Auth::user();

    //         if (!$user->relationLoaded('lectures')) {
    //             $user->load('lectures');
    //         }

    //         return response()->json([
    //             'isSubscribed' => $user->lectures->pluck('id')->contains((int)$id),
    //             'lecture_id' => $id
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => 'Server error',
    //             'message' => 'An error occurred while checking lecture subscription',
    //             'details' => config('app.debug') ? $e->getMessage() : null
    //         ], 500);
    //     }
    // }


    public function updateUsername(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userName' => [
                Rule::unique('admins', 'userName'),
                Rule::unique('users', 'userName'),
            ],
            [
                'userName.unique' => "Already Used"
            ]
        ]);
        if ($validator->fails())
            return response()->json([
                'success' => "false",
                'reason' => "Username Already Taken"
            ], 409);
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

    public function deleteSubs()
    {
        if (Auth::user()->privileges == 2) {
            foreach (User::all() as $user) {

                DB::transaction(function () use ($user) {

                    $user->lectures()->detach();

                    $user->subjects()->detach();
                });

            }
            $data = ['name' => "delete subs"];
            session(['update_info' => $data]);
            return redirect()->route('update.confirmation');
        } else {
            abort(403);
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
