<x-layout>
    <x-addcard : link="adduniversity" object="University">

        <div style="display:flex; flex-direction:column; align-items:center; margin-bottom:10%;">

        <div style="display:flex; flex-direction:column; align-items:center; margin-bottom:10%;">
            <label for="university_name">
                University Name:
            </label>
            <input type="text" name="university_name" id="university_name" value="{{ old('university_name') }}"
                autocomplete="off" style="height:20%; text-align:center; font-size:40%; width:fit-content;" required>
        </div>
        @error('university_name')
            <div class="error">{{ $message }}</div>
        @enderror
    </x-addcard>
</x-layout>
