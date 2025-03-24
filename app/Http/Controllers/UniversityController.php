<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\university;
use Illuminate\Support\Facades\Storage;

class UniversityController extends Controller
{
    //

    public function fetch($id)
    {
        $uni = university::find($id);
        if ($uni) {
            return response()->json([
                'success' => "true",
                'university' => $uni,
            ]);
        } else {
            return response()->json([
                'success' => "false"
            ]);
        }
    }

    public function fetchall()
    {
        return response()->json([
            'success' => "true",
            'universities' => university::all(),
        ]);
    }

    public function fetchTeachers($id)
    {
        $uni = university::find($id);
        if ($uni) {
            return response()->json([
                'successs' => "true",
                'teachers' => $uni->teachers
            ]);
        } else {
            return response()->json([
                'success' => "false",
                'reason' => "Unviersity Not Found"
            ], 404);
        }
    }

    public function edit(Request $request, $id)
    {
        $uniAttributes = $request->validate([
            'university_name' => [
                Rule::unique('universities', 'name')->ignore($id),
            ],
        ]);
        if (!$uniAttributes) {
            return redirect()->back()->withErrors([
                'university_name' => 'Name has alread been taken.'
            ]);
        }
        $uni = university::findOrFail($id);
        $teachers = json_decode($request->selected_objects, true);
        $uni->teachers()->sync($teachers);
        if (!is_null($request->file('object_image'))) {
            $path = $request->file('object_image')->store('Universities', 'public');
            if ($uni->image != "Universities/universityDefault.png") {
                Storage::disk('public')->delete($uni->image);
            }
            $uni->image = str_replace('public\\', '', $path);//this replaces what's already in the user logo for the recently stored new pic
        }
        $uni->name = $request->input('university_name');
        $uni->save();
        $data = ['element' => 'university', 'id' => $id, 'name' => $uni->name];
        session(['update_info' => $data]);
        return redirect()->route('update.confirmation')->with('link', '/universities');
    }
}
