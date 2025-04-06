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

            // 'deviceId' => 'required|string|unique:users,deviceId',

        ], [
            'userName.unique' => 'Already Used',
            'number.unique' => 'Already Used',

            // 'deviceId.unique' => 'Already Used',

        ]);//this will check if these are unique or already in use by other users
        //we return each one that wasn't unique so the frontend can highlight all the fields that are already in use

        if ($validator->fails()) {
            // Return all validation errors
            return response()->json([
                'success' => false,
                'reason' => $validator->errors(),
            ], 422);
        }
        $userAttributes = $request->validate([
            $userName = $request->input('userName'),
            $number = $request->input('number'),
            $password = Hash::make($request->input('password')),


            // $deviceId = $request->input('devceId'),

            $countryCode = "+963",
        ]);

        $user = User::create([
            'userName' => $userName,
            'number' => $number,
            'password' => $password,
            'countryCode' => $countryCode,
            'isBanned' => 0,
            'counter' => 0,
            // 'deviceId' => $deviceId,

        ]);
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
            'password' => 'required',

            // 'deviceId' => 'required',

        ]);

        // // Attempt to authenticate the user
        // if (Auth::guard('api')->attempt($credentials)) {
        // $credentials = $request->validate([
        //     'userName' => 'required',
        //     'password' => 'required',
        // ]);

        // // Find the user by userName
        $user = User::where('userName', $credentials['userName'])->first();
        if ($user && $user->isBanned) {
            return response()->json([
                'success' => false,
                'reason' => 'Banned',
            ], 401);
        }
        // Check if the user exists and the password is correct
        if ($user && Hash::check($credentials['password'], $user->password) /*&& $credentials['deviceId'] == $user->deviceId*/) {
            // Generate a token for the user
            $token = $user->createToken('API Token')->plainTextToken;
            $user->remember_token = $token;
            $user->save();
            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $user,
            ]);
        } else {


            // if (!Hash::check($credentials['password'], $user->password))


            return response()->json([
                'success' => false,
                'reason' => 'Invalid Credentials',
            ], 401);


            // elseif ($credentials['deviceId'] != $user->deviceId)
            //     return response()->json([
            //         'success' => false,
            //         'reason' => 'Unknown Device',
            //     ], 401);
        }

        // $loginData = $request->validate([
        //     'userName' => 'string|required|exists:users',
        //     'password' => 'required'
        // ]);
        // $credentials = request(['email', 'password']);

        // if(auth()->guard('user')->attempt($request->only('userName', 'password'))) {
        //     $user = User::query()->select('users.*')->find(auth()->guard('user')->user()['id']);
        //     $success = $user;
        //     $success['token'] = $user->createToken('API Token', ['user'])->accessToken;

        //     return response()->json('worked');
        // }
        // else {
        //     return response()->json('worked not');

        // }
    }

    public function banUser()
    {
        $user = Auth::user();

        // Validate user can be banned
        if ($user->isBanned) {
            return response()->json([
                'success' => false,
                'reason' => 'Already banned'
            ], 400);
        }

        $user->counter = 0;

        $user->isBanned = true;
        $user->save();

        $user->remember_token = null;
        $user->save();
        $user->currentAccessToken()->delete();

        return response()->json([
            'success' => true
        ]);
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

    public function loginView()
    {
        if (auth()->check()) {
            return redirect()->route('welcome');
        }
        return view('register');
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
