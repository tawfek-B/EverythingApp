@props(['university' => App\Models\university::findOrFail(session('university'))])
<x-layout>

    @php
        $assignedObjects = $university->teachers->pluck('id')->toArray();
    @endphp

    <x-editcard link="edituniversity/{{ session('university') }}" object="University" :objectModel=$university :image=true
        menu="Teacher" :menuModel="App\Models\Teacher::all()" relations="true" :subjects="$university->teachers" :selectedSubjects="$assignedObjects">
        <div>
            <div style="display:flex; flex-direction:column; align-items:center;">
                <label for="university_name" style="margin-bottom:10%;">
                    University Name:
                </label>
                <input type="text" name="university_name" id="university_name" value="{{ $university->name }}"
                    style="height:20%; text-align:center; font-size:40%; width:fit-content;margin-bottom:10%;">
            </div>
            @error('university_name')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="selected_teachers">
                Teachers:<br>
                (Click to remove and re-add)
            </label>
            <br>
    </x-editcard>
    </div>
</x-layout>
