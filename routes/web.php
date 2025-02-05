<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SessionController;

Route::middleware('auth.api')->get('/user', function(Request $request) {
    return $request->user();
});
Route::get('/', function () {
    return view('register');
});
// Route::post('/reg', [SessionController::class, 'adminlogin']);
Route::post('/weblogin', [SessionController::class, 'loginWeb']);
Route::get('/subjects', function() {
    return view('Admin/FullAdmin/Subjects');
});
Route::get('/teachers', function() {
    return view('Admin/FullAdmin/Teachers');
});
Route::get('/users', function() {
    return view('Admin/FullAdmin/Users');
});
Route::get('/admins', function() {
    return view('Admin/FullAdmin/Admins');
});
Route::get('/subject/{id}', function($id) {
    session(['subject'=>$id]);
    return view('Admin/FullAdmin/Subject');
});
Route::get('/teacher/{id}', function($id) {
    session(['teacher'=>$id]);
    return view('Admin/FullAdmin/Teacher');
});
Route::get('/user/{id}', function($id) {
    session(['user'=>$id]);
    return view('Admin/FullAdmin/User');
});
Route::get('/admin/{id}', function($id) {
    session(['admin'=>$id]);
    return view('Admin/FullAdmin/Admin');
});


Route::get('welcomeAdmin', function() {//auth
    return view ('Admin/FullAdmin/welcome');
});
Route::get('welcomeSemiAdmin', function() {//auth
    return view ('Admin/SemiAdmin/welcome');
});



Route::group(['middleware' => ['auth']], function () {
});
