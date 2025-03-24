<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{

    public function add(Request $request)
    {
        // dd($request->all());
        $validator = $request->validate([
            'admin_name' => [
                Rule::unique('admins', 'name'),
                Rule::unique('teachers', 'name'),
            ],
            'admin_user_name' => [
                Rule::unique('admins', 'userName'),
                Rule::unique('teachers', 'userName'),
                Rule::unique('users', 'userName')
            ],
            'admin_number' => [
                Rule::unique('admins', 'number'),
                Rule::unique('users', 'number')
            ],
        ]);

        if (!$validator) {

            return redirect()->back()->withErrors([
                'admin_name' => 'Name has already been taken',
                'admin_user_name' => 'User name has already been taken',
                'admin_number' => 'Number has already been taken',
            ]);
        }

        if (!is_null($request->file('object_image'))) {
            $path = $request->file('object_image')->store('Admins', 'public');
        } else {
            $path = "Admins/adminDefault.png";
        }
        $adminAttributes = [
            'name' => $request->input('admin_name'),
            'userName' => $request->input('admin_user_name'),
            'countryCode' => '+963',
            'number' => $request->input('admin_number'),
            'password' => Hash::make($request->input('admin_password')),
            'image' => $path,
            'privileges' => 1,
        ];
        $admin = Admin::create($adminAttributes);
        if ($request->input('admin_privileges') == 'Admin') {
            $admin->privileges = 2;
        } else if ($request->input('admin_privileges') == 'Semi-Admin') {
            $admin->privileges = 1;
        }
        $admin->save();
        $data = ['element' => 'product', 'id' => $admin->id, 'name' => $admin->name];
        session(['add_info' => $data]);
        return redirect()->route('add.confirmation')->with('link', '/admins');
    }

    public function edit(Request $request, $id)
    {

        $validator = $request->validate([
            'admin_name' => [
                Rule::unique('admins', 'name')->ignore($id),
                Rule::unique('teachers', 'name')->ignore(Admin::findOrFail($id)->teacher_id)
            ],
            'admin_user_name' => [
                Rule::unique('admins', 'userName')->ignore($id),
                Rule::unique('teachers', 'userName')->ignore(Admin::findOrFail($id)->teacher_id),
                Rule::unique('users', 'userName')
            ],
            'admin_number' => [
                Rule::unique('admins', 'number')->ignore($id),
                Rule::unique('users', 'number')
            ],
        ]);
        if (!$validator) {

            return redirect()->back()->withErrors([
                'admin_name' => 'Name has already been taken',
                'admin_user_name' => 'User name has already been taken',
                'admin_number' => 'Number has already been taken',
            ]);
        }
        $admin = Admin::findOrFail($id);
        if (!is_null($request->file('object_image'))) {
            $path = $request->file('object_image')->store('Admins', 'public');
            if ($admin->image != "Admins/adminDefault.png") {
                Storage::disk('public')->delete($admin->image);
            }
            $admin->image = str_replace('public\\', '', $path);//this replaces what's already in the user logo for the recently stored new pic
        }
        $admin->name = $request->admin_name;
        $admin->userName = $request->admin_user_name;
        $admin->countryCode = '+963';
        $admin->number = $request->admin_number;
        if (!is_null($request->file('object_image')))

            $admin->image = $path;
        if ($request->admin_privileges == "Semi-Admin")
            $admin->privileges = 1;
        if ($request->admin_privileges == "Admin")
            $admin->privileges = 2;
        $admin->save();
        if (Auth::id() == session('admin')) {
            session()->flush();
            Auth::logout();
            return redirect('/');
        } else {
            $data = ['element' => 'admin', 'id' => $id, 'name' => $admin->name];
            session(['update_info' => $data]);
            return redirect()->route('update.confirmation')->with('link', '/admins');
        }
    }
    //
    public function delete($id)
    {
        $admin = Admin::findOrFail($id);
        $name = $admin->name;
        $admin->delete();
        if ($admin->privileges == 0) {
            $teacher = Teacher::findOrFail($admin->teacher_id);
            $teacher->delete();
        }
        $data = ['element' => 'admin', 'name' => $name];
        session(['delete_info' => $data]);
        return redirect()->route('delete.confirmation')->with('link', '/admins');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/');
    }
}
