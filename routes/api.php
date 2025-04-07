<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Lecture;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LectureController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\FileController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/getuser/{id}', [UserController::class, 'fetch']);
Route::post('/register', [SessionController::class, 'createUser']);
Route::post('/login', [SessionController::class, 'loginUser']);


//test
// Route::post('/registerteacher', [TeacherController::class, 'add']);

// Route::get('/subject/{id}/lectures', function($id) {
//     return response()->json(['lectureCount' => $lectureCount]);
// });

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('/getuser', [UserController::class, 'fetchAuth']);
    Route::get('/getuser/{id}', [UserController::class, 'fetch']);
    Route::get('/getusersubjects', [UserController::class, 'fetchSubjects']);
    Route::get('/getuserlectures', [UserController::class, 'fetchLectures']);
    Route::get('/getusersubscriptions', [UserController::class, 'fetchSubs']);
    Route::get('/getallusers', [UserController::class, 'fetchAll']);
    Route::get('/subjectissubscribed/{id}', [UserController::class, 'confirmSubSub']);
    Route::get('/lectureissubscribed/{id}', [UserController::class, 'confirmLecSub']);
    Route::put('/counter', [UserController::class, 'editCounter']);
    Route::put('/changepassword', [UserController::class, 'updatePassword']);
    Route::put('/changeusername', [UserController::class, 'updateUsername']);

    Route::get('/getteacher/{id}', [TeacherController::class, 'fetch']);
    Route::get('/getteachersubjects/{id}', [TeacherController::class, 'fetchSubjects']);
    Route::get('/getteachersubjectsnames/{id}', [TeacherController::class, 'fetchSubjectsNames']);
    Route::get('/getteacheruniversities/{id}', [TeacherController::class, 'fetchUnis']);
    Route::get('/getallteachers', [TeacherController::class, 'fetchAll']);

    Route::get('/getuniversity/{id}', [UniversityController::class, 'fetch']);
    Route::get('/getuniversityteachers/{id}', [UniversityController::class, 'fetchTeachers']);
    Route::get('/getalluniversities', [UniversityController::class, 'fetchall']);

    Route::get('/getsubject/{id}', [SubjectController::class, 'fetch']);
    Route::get('/getsubjectlectures/{id}', [SubjectController::class, 'fetchLectures']);
    Route::get('/getsubjectteachers/{id}', [SubjectController::class, 'fetchTeachers']);
    Route::get('/getsubjectusers/{id}', [SubjectController::class, 'fetchUsers']);
    Route::get('/getallsubjects', [SubjectController::class, 'fetchAll']);

    Route::get('/getlecture/{id}', [LectureController::class, 'fetch']);
    Route::get('/getlecturefile360/{id}', [LectureController::class, 'fetchFile360']);
    Route::get('/getlecturefile720/{id}', [LectureController::class, 'fetchFile720']);
    Route::get('/getlecturefile1080/{id}', [LectureController::class, 'fetchFile1080']);

    Route::get('/getteacherimage/{id}', [ImageController::class, 'fetchTeacher']);
    Route::get('/getlectureimage/{id}', [ImageController::class, 'fetchLecture']);
    Route::get('/getsubjectimage/{id}', [ImageController::class, 'fetchSubject']);
    Route::get('/getuniversityimage/{id}', [ImageController::class, 'fetchUniversity']);

    // Route::get('/getuser', [SessionController::class, 'test']);
    Route::post('/logout', [SessionController::class, 'logoutUser'])->name('logout.user');
    Route::post('/ban', [SessionController::class, 'banUser'])->name('ban.user');

    // Route::get('/url/{videoId}/{quality}', [FileController::class, 'encryptAndGenerateUrl']);

    // Route::get('/download-encrypted-video/{file}', [FileController::class, 'serveEncryptedFile'])
    //     ->name('download.encrypted.video');

});
