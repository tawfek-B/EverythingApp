@props(['lecture' => App\Models\Lecture::findOrFail(session('lecture'))])
<x-layout>
    <x-editcard : link="editlecture/{{ session('lecture') }}" object="Lecture" :objectModel=$lecture :image=true>
        <div>
            <div style="display:flex; flex-direction:column; align-items:center;">
                <label for="lecture_name">
                    Lecture Name:
                </label>
                <input type="text" name="lecture_name" id="lecture_name" value="{{ $lecture->name }}"
                    style="height:20%; text-align:center; font-size:40%; width:fit-content;" />
            </div>
            <br>

            <div style="display:flex; flex-direction:column; align-items:center;">
                <label for="lecture_description">
                    Lecture Description:
                </label>

                <textarea name="lecture_description" id="lecture_description" autocomplete="off"
                    style="height:150px; width:80%; font-size:16px; padding:10px; resize:vertical;" required>{{ $lecture->description }}</textarea>
            </div>
            <br>


    </x-editcard>
    </div>
</x-layout>
