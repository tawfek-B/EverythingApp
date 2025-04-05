@props(['user' => App\Models\User::findOrFail(session('user'))])
<x-layout>

    @php
        $assignedObjects = $user->subjects->pluck('id')->toArray();
    @endphp

    <x-editcard :selectedSubjects="$assignedObjects" link="edituser/{{ session('user') }}" relations="true" :subjects="$user->subjects" object="User" :model=$user
        menu="Subject" :menuModel="App\Models\Subject::all()" :lectures=true>
        <div>


            <div style="display:flex; flex-direction:column; align-items:center;">
                <label for="user_name" style="margin-bottom:10%;">
                    User Name:
                </label>
                <input type="text" name="user_name" id="user_name" value="{{ $user->userName }}"
                    style="height:20%; text-align:center; font-size:40%; width:fit-content;margin-bottom:10%;" readonly>
            </div>
            @error('user_name')
                <div class="error">{{ $message }}</div>
            @enderror
            <div style="display:flex; flex-direction:column; align-items:center;">
                <label for="user_number">
                    User Number:
                </label>
                <div style="position: relative; width: fit-content; height:fit-content;">
                    <input type="text" name="user_number" id="user_number" placeholder="9XXXXXXXX" value="{{$user->number}}" autocomplete="off"
                           inputmode="numeric" style="height: 20%; text-align: left; font-size: 40%;text-indent:30%; width: 100%; box-sizing: border-box; @error('user_number') border:2px solid red @enderror"
                           oninput="if (this.value.length > 9) this.value = this.value.slice(0, 9); this.value = this.value.replace(/(?!^)\+/g,'').replace(/[^0-9+]/g, '')" pattern="[0-9]{9}" readonly>
                    <span style="position: absolute; left:3px; top: 60%; transform: translateY(-50%); font-size: 50%; color: #000; pointer-events: none;">+963</span>
                    <div style="position: absolute; left: 40px; top: 42.5%; height: 34%; width: 1px; background-color: #000;"></div>
                </div>
            </div>

            @error('user_number')
                <div class="error">{{ $message }}</div>
            @enderror
            <div style="background-color: black; width:100%; height:1px; margin-top:5%; margin-bottom:5%;"></div>
            <div>
                <div style="margin-bottom:3%;">

                    <strong>Subscriptions</strong>
                </div>

                <br>
                <label for="selected_objects">
                    Subjects:<br>
                    (Click to remove and re-add)
                </label>
                <br>
            </div>

    </x-editcard>
    </div>
</x-layout>
