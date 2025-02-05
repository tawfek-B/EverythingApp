<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class SessionController extends Controller
{

    public function createUser(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'userName' => 'required|string|unique:users,userName',
            'number' => 'required|string|unique:users,number',
        ], [
            'userName.unique' => 'Already Used',
            'number.unique' => 'Already Used',
        ]);//this will check if these are unique or already in use by other users
        //we return each one that wasn't unique so the frontend can highlight all the fields that are already in use

        if ($validator->fails()) {
            // Return all validation errors
            return response()->json([
                'Success' => false,
                'Errors' => $validator->errors(),
            ], 422);
        }
        $userAttributes = $request->validate([
            $userName = 'userName' => ['required'],
            $number = 'number' => ['required'],
            $password = 'password' => ['required'],
        ]);

        $user = User::create($userAttributes);
        $token = $user->createToken('API Token Of' . $user->name)->plainTextToken;
        $user->remember_token = $token;
        $user->save();
        Auth::login($user);
        return response()->json(['success' => 'true', 'token' => $token, 'user' => $user]);//we return a "success" field so the frontend can see if the sign up process failed or not

    }
    public function loginUser(Request $request)
    {
        $credentials = $request->validate([
            'userName' => 'required',
            'number' => 'required',
            'password' => 'required'
        ]);
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('API Token Of' . $user->name)->plainTextToken;
            $user->remember_token = $token;
            $user->save();
            return response()->json([
                'success' => "true",
                'token' => $user->remember_token,
                'user' => $user,
            ]);
        } else {
            $isFound = false;
            $isMatching = false;
            foreach (User::all() as $user) {
                if ($user->userName == $request->input('userName')) {
                    if ($user->number == $request->input('number'))
                        $isMatching = true;
                    $isFound = true;
                    break;
                }
            }
            if ($isFound && $isMatching) {
                return response()->json([
                    'success' => "false",
                    'reason' => "Wrong Password",
                ]);
            } else if ($isFound && !$isMatching) {
                return response()->json([
                    'success' => "false",
                    'reason' => "Wrong Number",
                ]);
            } else {
                return response()->json([
                    'success' => "false",
                    'reason' => "Wrong user name",
                ]);
            }
        }
    }

    public function logoutUser()
    {

        Auth::user()->remember_token = null;
        Auth::user()->save();
        Auth::user()->currentAccessToken()->delete();


        return response()->json([
            'success' => 'true',
        ]);
    }

    public function test()
    {
        $user = Auth::user();
        return response()->json([
            'User' => $user
        ]);
    }

    public function loginWeb(Request $request)
    {
        $credentials = ['userName'=>$request->userName, 'password'=>$request->password];
        if(Auth::attempt($credentials)) {
            $admin = Admin::where('userName', $credentials['userName'])->first();
            if (Hash::check($credentials['password'], $admin->password)) {
                Auth::login($admin);
                if ($admin->privileges == 2)
                    return redirect('/welcomeAdmin');
                else if($admin->privileges == 1)
                return view('Admin/SemiAdmin/welcome');
                else if($admin->privileges == 0)
                    return redirect('/welcomeAdmin');
            }
        }
        return redirect()->back()->withErrors(['password' => 'Incorrect Credentials'])->withInput(['userName']);

    }
}
