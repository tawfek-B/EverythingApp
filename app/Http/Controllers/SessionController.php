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
use Illuminate\Support\Facades\Log;
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
            $userName = $request->input('userName'),
            $number = $request->input('number'),
            $password = Hash::make($request->input('password')),
            $countryCode = "+963",
        ]);

        $user = User::create([
            'userName' => $userName,
            'number' => $number,
            'password' => $password,
            'countryCode' => $countryCode,
        ]);
        $token = $user->createToken('API Token Of' . $user->name)->plainTextToken;
        $user->remember_token = $token;
        $user->save();
        Auth::login($user);
        return response()->json(['success' => 'true', 'token' => $token, 'user' => $user]);//we return a "success" field so the frontend can see if the sign up process failed or not

    }
    public function loginUser(Request $request)
    {
        // $credentials = $request->validate([
        //     'userName' => 'required',
        //     'password' => 'required',
        // ]);

        // // Attempt to authenticate the user
        // if (Auth::guard('api')->attempt($credentials)) {
            $credentials = $request->validate([
                'userName' => 'required',
                'password' => 'required',
            ]);

            // Find the user by userName
            $user = User::where('userName', $credentials['userName'])->first();

            // Check if the user exists and the password is correct
            if ($user && Hash::check($credentials['password'], $user->password)) {
                // Generate a token for the user
                $token = $user->createToken('API Token')->plainTextToken;
                $user->remember_token = $token;
                return response()->json([
                    'success' => true,
                    'token' => $token,
                    'user' => $user,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'reason' => 'Invalid credentials',
                ], 401);
            }
    }


    public function logoutUser()
    {
        Auth::user()->remember_token = null;
        Auth::user()->save();
        Auth::user()->currentAccessToken()->delete();

        // dd(Auth::user());

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
        $credentials = ['userName' => $request->userName, 'password' => $request->password];
        if (Auth::attempt($credentials)) {
            $admin = Admin::where('userName', $credentials['userName'])->first();
            if (Hash::check($credentials['password'], $admin->password)) {
                Auth::login($admin);
                return redirect('/welcome');
            }
        }
        return redirect()->back()->withErrors(['password' => 'Invalid Credentials'])->withInput(['userName']);

    }
}
