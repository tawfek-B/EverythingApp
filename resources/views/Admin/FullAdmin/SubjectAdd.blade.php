<x-layout>
    <x-addcard : link="addsubject" object="Subject">
        <div style="display:flex; flex-direction:column; align-items:center; margin-bottom:10%;">
            <label for="subject_name">
                Subject Name:
            </label>
            <input type="text" name="subject_name" id="subject_name" value="{{ old('subject_name') }}"
                autocomplete="off" style="height:20%; text-align:center; font-size:40%; width:fit-content;" required>
        </div>
        @error('subject_name')
            <div class="error">{{ $message }}</div>
        @enderror
    </x-addcard>
</x-layout>
