<?php

use App\Http\Controllers\UniversityController;
use App\Models\Teacher;
use App\Models\university;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LectureController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Lecture;
use App\Models\Subject;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;

Route::middleware('auth.api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get(
    '/',
    [SessionController::class, 'loginView']
)->name('login');
// Route::post('/reg', [SessionController::class, 'adminlogin']);
Route::post('/weblogin', [SessionController::class, 'loginWeb']);



Route::group(['middleware' => ['auth']], function () {
    Route::get('/check-views', function () {
        return [
            'admin_subjects' => View::exists('Admin/FullAdmin/Subjects'),
            'teacher_subjects' => View::exists('Teacher/Subjects'),
            'admin_lectures' => View::exists('Admin/FullAdmin/Lectures'),
            'admin_universities' => View::exists('Admin/FullAdmin/Universities'),
        ];
    });
    Route::get('/subjects', function () {
        if (Auth::user()->privileges == 2)
            return view('Admin/FullAdmin/Subjects');
        elseif (Auth::user()->privileges == 0)
            return view('Teacher/Subjects');
        else
            return abort(404);

    });
    Route::get('/teachers', function () {
        if (Auth::user()->privileges == 2)
            return view('Admin/FullAdmin/Teachers');
        else
            return abort(404);
    });

    Route::get('/users', function (Request $request) {
        // Get the user IDs subscribed to the subject
        $userIDs = User::all()->pluck('id')->toArray();

        // Fetch the users
        $users = User::whereIn('id', $userIDs)->get();

        // Pagination settings
        $perPage = 10; // Number of items per page
        $currentPage = $request->input('page', 1); // Get the current page from the request
        $offset = ($currentPage - 1) * $perPage;

        // Slice the collection to get the items for the current page
        $currentPageItems = $users->slice($offset, $perPage)->values();

        // Create a LengthAwarePaginator instance
        $paginatedUsers = new LengthAwarePaginator(
            $currentPageItems, // Items for the current page
            $users->count(), // Total number of items
            $perPage, // Items per page
            $currentPage, // Current page
            ['path' => $request->url(), 'query' => $request->query()] // Additional options
        );

        if (Auth::user()->privileges == 2)
            return view('Admin/FullAdmin/Users', ['users' => $users, 'sub' => false]);
        elseif (Auth::user()->privileges == 1)
            return view('Admin/SemiAdmin/Users', ['users' => $users]);
        else
            return abort(404);
    });
    Route::get('/admins', function () {
        if (Auth::user()->privileges == 2)
            return view('Admin/FullAdmin/Admins');
        else
            return abort(404);
    });
    Route::get('/lectures', function () {
        if (Auth::user()->privileges == 2)
            return view('Admin/FullAdmin/Lectures', ['lec' => false]);
        elseif (Auth::user()->privileges == 0)
            return view('Teacher/Lectures', ['lec' => false]);
        else
            return abort(404);
    });
    Route::get('/universities', function () {
        if (Auth::user()->privileges == 2)
            return view('Admin/FullAdmin/Universities');
        else
            return abort(404);
    });
    Route::get('/subject/{id}', function ($id) {
        if (Auth::user()->privileges == 2) {
            session(['subject' => $id]);
            return view('Admin/FullAdmin/Subject');
        } elseif (Auth::user()->privileges == 0) {
            session(['subject' => $id]);
            return view('Teacher/Subject');
        } else
            return abort(404);
    });
    Route::get('/university/{id}', function ($id) {
        if (Auth::user()->privileges == 2) {
            session(['university' => $id]);
            return view('Admin/FullAdmin/University');
        } else
            return abort(404);
    });
    Route::get('/teacher/{id}', function ($id) {
        if (Auth::user()->privileges == 2) {
            session(['teacher' => $id]);
            return view('Admin/FullAdmin/Teacher');
        } else
            return abort(404);
    });
    Route::get('/user/{id}', function ($id) {
        session(['user' => $id]);
        if (Auth::user()->privileges == 2)
            return view('Admin/FullAdmin/User');
        elseif (Auth::user()->privileges == 1)
            return view('Admin/SemiAdmin/User');
        else
            return abort(404);

    });
    Route::get('/admin/{id}', function ($id) {
        if (Auth::user()->privileges == 2) {
            session(['admin' => $id]);
            return view('Admin/FullAdmin/Admin');
        } else
            return abort(404);
    });
    Route::get('/lecture/{id}', function ($id) {
        if (Auth::user()->privileges == 2) {
            session(['lecture' => $id]);
            return view('Admin/FullAdmin/Lecture');
        } elseif (Auth::user()->privileges == 0) {
            session(['lecture' => $id]);
            return view('Teacher/Lecture');
        } else
            return abort(404);
    });


    Route::get('/addadmin', function () {
        if (Auth::user()->privileges == 2)
            return view('Admin/FullAdmin/AdminAdd');
        else
            return abort(404);
    });
    Route::post('/addadmin', [AdminController::class, 'add']);

    Route::get('/addlecture', function () {
        if (Auth::user()->privileges == 2)
            return view('Admin/FullAdmin/LectureAdd');
        elseif (Auth::user()->privileges == 0)
            return view('Teacher/LectureAdd');
        else
            return abort(404);
    });

    Route::get('/subject/addlecture/{id}', function ($id) {
        if (Auth::user()->privileges == 0)
            return view('Teacher/LectureAdd', with(['subjectID' => $id]));
        else
            return abort(404);
    });
    Route::post('/addlecture', [LectureController::class, 'add'])->name('addlecture');

    Route::get('/addsubject', function () {
        if (Auth::user()->privileges == 2)
            return view('Admin/FullAdmin/SubjectAdd');
        else
            return abort(404);
    });
    Route::post('/addsubject', [SubjectController::class, 'add']);

    Route::get('/addteacher', function () {
        if (Auth::user()->privileges == 2)
            return view('Admin/FullAdmin/TeacherAdd');
        else
            return abort(404);
    });
    Route::post('/addteacher', [TeacherController::class, 'add']);

    // Route::get('/adduser', function () {
    //     if (Auth::user()->privileges == 2)
    //         return view('Admin/FullAdmin/UserAdd');
    // });
    // Route::post('/adduser', [UserController::class, 'add']);


    Route::get('/subject/edit/{id}', function ($id) {
        if (Auth::user()->privileges == 2) {
            session(['subject' => $id]);
            return view('Admin/FullAdmin/SubjectEdit', ['teachers' => []]);
        } else
            return abort(404);
    });


    Route::put('/editsubject/{id}', [SubjectController::class, 'edit']);
    Route::delete('/deletesubject/{id}', [SubjectController::class, 'delete']);

    Route::get('/teacher/edit/{id}', function ($id) {
        if (Auth::user()->privileges == 2) {

            session(['teacher' => $id]);
            return view('Admin/FullAdmin/TeacherEdit', ['subjects' => []]);
        } else
            return abort(404);
    })->name('teacher.edit');//might have to change this


    Route::put('/editteacher/{id}', [TeacherController::class, 'edit']);
    Route::delete('/deleteteacher/{id}', [TeacherController::class, 'delete']);

    Route::get('/user/edit/{id}', function ($id) {
        session(['user' => $id]);
        if (Auth::user()->privileges == 2)
            return view('Admin/FullAdmin/UserEdit');
        elseif (Auth::user()->privileges == 1)
            return view('Admin/SemiAdmin/UserEdit');
        else
            return abort(404);
    });

    Route::put('/edituser/{id}', [UserController::class, 'edit']);
    Route::delete('/deleteuser/{id}', [UserController::class, 'delete']);

    Route::get('/admin/edit/{id}', function ($id) {
        if (Auth::user()->privileges == 2) {
            session(['admin' => $id]);
            if (Admin::findOrFail($id)->privileges != 0)
                return view('Admin/FullAdmin/AdminEdit', ['subjects' => []]);
            session(['teacher' => Admin::findOrFail($id)->teacher_id]);
            return redirect()->route('teacher.edit', ['id' => session('teacher')]);
        } else
            return abort(404);

    });
    Route::get('/adduniversity', function () {
        if (Auth::user()->privileges == 2)
            return view('Admin/FullAdmin/UniversityAdd');
        else
            return abort(404);
    });
    Route::post('/adduniversity', [UniversityController::class, 'add']);

    Route::get('/university/edit/{id}', function ($id) {
        if (Auth::user()->privileges == 2) {
            session(['university' => $id]);
            return view('Admin/FullAdmin/UniversityEdit');
        } else
            return abort(404);
    });

    Route::put('/edituniversity/{id}', [UniversityController::class, 'edit']);
    Route::delete('/deleteuniversity/{id}', [UniversityController::class, 'delete']);


    Route::put('/editadmin/{id}', [AdminController::class, 'edit']);
    Route::delete('/deleteadmin/{id}', [AdminController::class, 'delete']);


    Route::get('/lecture/edit/{id}', function ($id) {
        if (Auth::user()->privileges == 2) {
            session(['lecture' => $id]);
            return view('Admin/FullAdmin/LectureEdit');
        }
        if (Auth::user()->privileges == 0) {
            session(['lecture' => $id]);
            return view('Teacher/LectureEdit');
        } else
            return abort(404);
    });

    Route::put('/editlecture/{id}', [LectureController::class, 'edit']);
    Route::delete('/deletelecture/{id}', [LectureController::class, 'delete']);

    Route::get('/university/{id}/teachers', function ($id, Request $request) {
        // Store the university ID in the session
        if (Auth::user()->privileges == 2) {
            session(['university' => $id]);

            // Get the lecture IDs for the university
            $teacherIDs = university::findOrFail($id)->teachers->pluck('id')->toArray();

            // Fetch the teachers
            $teachers = Teacher::whereIn('id', $teacherIDs)->get();

            // Pagination settings
            $perPage = 10; // Number of items per page
            $currentPage = $request->input('page', 1); // Get the current page from the request
            $offset = ($currentPage - 1) * $perPage;

            // Slice the collection to get the items for the current page
            $currentPageItems = $teachers->slice($offset, $perPage)->values();

            // Create a LengthAwarePaginator instance
            $paginatedTeachers = new LengthAwarePaginator(
                $currentPageItems, // Items for the current page
                $teachers->count(), // Total number of items
                $perPage, // Items per page
                $currentPage, // Current page
                ['path' => $request->url(), 'query' => $request->query()] // Additional options
            );
            // Pass the paginated lectures to the view
            return view('Admin/FullAdmin/Teachers', ['teachers' => $teachers]);
        } else
            return abort(404);
    });

    Route::get('/subject/{id}/users', function ($id, Request $request) {
        if (Auth::user()->privileges == 2) {
            session(['subject' => $id]);

            // Get the user IDs subscribed to the subject
            $userIDs = Subject::findOrFail($id)->users->pluck('id')->toArray();

            // Fetch the users
            $users = User::whereIn('id', $userIDs)->get();

            // Pagination settings
            $perPage = 10; // Number of items per page
            $currentPage = $request->input('page', 1); // Get the current page from the request
            $offset = ($currentPage - 1) * $perPage;

            // Slice the collection to get the items for the current page
            $currentPageItems = $users->slice($offset, $perPage)->values();

            // Create a LengthAwarePaginator instance
            $paginatedUsers = new LengthAwarePaginator(
                $currentPageItems, // Items for the current page
                $users->count(), // Total number of items
                $perPage, // Items per page
                $currentPage, // Current page
                ['path' => $request->url(), 'query' => $request->query()] // Additional options
            );

            // Pass the paginated users to the view
            return view('Admin/FullAdmin/Users', ['users' => $paginatedUsers, 'sub' => true]);
        } else
            return abort(404);
    });

    Route::get('/subject/{id}/lectures', function ($id, Request $request) {
        // Store the subject ID in the session
        session(['subject' => $id]);

        // Get the lecture IDs for the subject
        $lectureIDs = Subject::findOrFail($id)->lectures->pluck('id')->toArray();

        // Fetch the lectures
        $lectures = Lecture::whereIn('id', $lectureIDs)->get();

        // Pagination settings
        $perPage = 10; // Number of items per page
        $currentPage = $request->input('page', 1); // Get the current page from the request
        $offset = ($currentPage - 1) * $perPage;

        // Slice the collection to get the items for the current page
        $currentPageItems = $lectures->slice($offset, $perPage)->values();

        // Create a LengthAwarePaginator instance
        $paginatedLectures = new LengthAwarePaginator(
            $currentPageItems, // Items for the current page
            $lectures->count(), // Total number of items
            $perPage, // Items per page
            $currentPage, // Current page
            ['path' => $request->url(), 'query' => $request->query()] // Additional options
        );

        if (Auth::user()->privileges == 2) {
            // Pass the paginated lectures to the view
            return view('Admin/FullAdmin/Lectures', ['lectures' => $paginatedLectures, 'lec' => true]);
        } elseif (Auth::user()->privileges == 0) {
            return view('Teacher/Lectures', ['lectures' => $paginatedLectures, 'lec' => true]);
        } else
            return abort(404);
    });
    Route::get('/user/{id}/lectures', function ($id, Request $request) {
        // Store the subject ID in the session
        if (Auth::user()->privileges == 2) {
            session(['user' => $id]);

            // Get the lecture IDs for the subject
            $lectureIDs = User::findOrFail($id)->lectures->pluck('id')->toArray();

            // Fetch the lectures
            $lectures = Lecture::whereIn('id', $lectureIDs)->get();

            // Pagination settings
            $perPage = 10; // Number of items per page
            $currentPage = $request->input('page', 1); // Get the current page from the request
            $offset = ($currentPage - 1) * $perPage;

            // Slice the collection to get the items for the current page
            $currentPageItems = $lectures->slice($offset, $perPage)->values();

            // Create a LengthAwarePaginator instance
            $paginatedLectures = new LengthAwarePaginator(
                $currentPageItems, // Items for the current page
                $lectures->count(), // Total number of items
                $perPage, // Items per page
                $currentPage, // Current page
                ['path' => $request->url(), 'query' => $request->query()] // Additional options
            );

            // Pass the paginated lectures to the view
            return view('Admin/FullAdmin/Lectures', ['lectures' => $paginatedLectures, 'lec' => true, 'user' => true]);
        } else
            return abort(404);
    });

    Route::put('/deletesubs', [UserController::class, 'deleteSubs']);

    Route::get('/test', function () {
        dd(Subject::withCount('users')->find(16));
    });
    Route::get('lecture/show/{id}/360', [FileController::class, 'show360'])->name('file360.show');
    Route::get('lecture/show/{id}/720', [FileController::class, 'show720'])->name('file720.show');
    Route::get('lecture/show/{id}/1080', [FileController::class, 'show1080'])->name('file1080.show');

    Route::get('/welcome', function () {
        if (Auth::user()->privileges == 2)
            return view('Admin/FullAdmin/welcome');
        else if (Auth::user()->privileges == 1)
            return view('Admin/SemiAdmin/welcome');
        else if (Auth::user()->privileges == 0)
            return view('Teacher/welcome');
        else
            return redirect('/');
    })->name('welcome');
    Route::get('/confirmupdate', function () {
        return view(view: 'confirmedUpdate');
    })->name('update.confirmation');

    Route::get('/confirmadd', function () {
        return view(view: 'confirmedAdd');
    })->name('add.confirmation');

    Route::get('/confirmdelete', function () {
        return view(view: 'confirmedDelete');
    })->name('delete.confirmation');

    Route::get('/confirmlogout', function () {
        return view(view: 'confirmedLogout');
    })->name('logout.confirmation');

    Route::post('/logout', function (Request $request) {
        return redirect()->route('logout.confirmation');
    });
    Route::post('/registerout', [AdminController::class, 'logout']);
});