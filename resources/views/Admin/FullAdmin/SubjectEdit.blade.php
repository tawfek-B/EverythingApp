@props(['subject' => App\Models\Subject::findOrFail(session('subject'))])
<x-layout>

    @php
        $assignedObjects = $subject->teachers->pluck('id')->toArray();
    @endphp
    <x-editcard :selectedSubjects="$assignedObjects" link="editsubject/{{ session('subject') }}" relations="true" :subjects="$subject->teachers"
        object="Subject" :objectModel=$subject menu="Teacher" :menuModel="App\Models\Teacher::all()" image="true">
        <div>
            <div style="display:flex; flex-direction:column; align-items:center;">
                <label for="subject_name">
                    Subject Name:
                </label>
                <input type="text" name="subject_name" id="subject_name"
                    value="{{ App\Models\Subject::where('id', session('subject'))->first()->name }}"
                    style="height:20%; text-align:center; font-size:40%; width:fit-content;" />
            </div>
            @error('subject_name')
                <div class="error">{{ $message }}</div>
            @enderror
            <br>
            <label for="selected_teachers">
                Teachers:<br>
                (Click to remove and re-add)
            </label>
            <br>

    </x-editcard>
    </div>
</x-layout>
