<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\File;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\TeacherController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/getuser/{id}', [UserController::class, 'fetch']);
Route::post('/register', [SessionController::class, 'createUser']);
Route::post('/login', action: [SessionController::class, 'login']);


//test
Route::post('/registerteacher', [TeacherController::class, 'add']);

Route::get('/subject/{id}/lectures', function($id) {
    $lectureCount = File::where('id', $id)->count();
    return response()->json(['lectureCount' => $lectureCount]);
});

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('/getuser', [SessionController::class, 'test']);
    Route::post('/logout', [SessionController::class, 'logout']);

    Route::get('/getfile', [FileController::Class, 'test']);

});
