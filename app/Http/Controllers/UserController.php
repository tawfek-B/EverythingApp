<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends Controller
{
    public function fetch($id) {
        $found = ($user = User::where('id', $id)->first())?true:false;
        return response()->json([
            'Success' => $found,
            'User' => $user
        ]);
    }
}
