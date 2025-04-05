@props(['user' => App\Models\User::findOrFail(session('user'))])

<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #f44336;
    }

    input:checked+.slider:before {
        transform: translateX(26px);
    }

    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>

<x-layout>

    @php
        $assignedObjects = $user->subjects->pluck('id')->toArray();
    @endphp

    <x-editcard :selectedSubjects="$assignedObjects" link="edituser/{{ session('user') }}" relations="true" :subjects="$user->subjects" object="User"
        :model="$user" menu="Subject" :menuModel="App\Models\Subject::all()" :lectures=true :isBanned="$user->isBanned">
        <div>


            <div style="display:flex; flex-direction:column; align-items:center;">
                <label for="user_name" style="margin-bottom:10%;">
                    User Name:
                </label>
                <input type="text" name="user_name" id="user_name" value="{{ $user->userName }}"
                    style="height:20%; text-align:center; font-size:40%; width:fit-content;margin-bottom:10%;">
            </div>
            @error('user_name')
                <div class="error">{{ $message }}</div>
            @enderror
            <div style="display:flex; flex-direction:column; align-items:center;">
                <label for="user_number">
                    User Number:
                </label>
                <div style="position: relative; width: fit-content; height:fit-content;">
                    <input type="text" name="user_number" id="user_number" placeholder="9XXXXXXXX"
                        value="{{ $user->number }}" autocomplete="off" inputmode="numeric"
                        style="height: 20%; text-align: left; font-size: 40%;text-indent:30%; width: 100%; box-sizing: border-box; @error('user_number') border:2px solid red @enderror"
                        oninput="if (this.value.length > 9) this.value = this.value.slice(0, 9); this.value = this.value.replace(/(?!^)\+/g,'').replace(/[^0-9+]/g, '')"
                        pattern="[0-9]{9}" required>
                    <span
                        style="position: absolute; left: 5px; top: 50%; transform: translateY(-50%); font-size: 40%; color: #000; pointer-events: none;">+963</span>
                    <div
                        style="position: absolute; left: 40px; top: 0%; height: 100%; width: 1px; background-color: #000;">
                    </div>
                </div>
            </div>

            @error('user_number')
                <div class="error">{{ $message }}</div>
            @enderror
            <div
                style="margin-top: 20px; display: flex; align-items: center; flex-direction:column; justify-content: space-between; margin-left:auto; margin-right:auto; width:fit-content">
                <div>
                    <label for="isBanned" style="font-weight: bold;">
                        User Status:
                    </label>
                    <span style="margin-left: 10px;">
                        {{ $user->isBanned ? 'Banned' : 'Active' }}
                    </span>
                </div>
                <label class="switch">
                    <input type="checkbox" name="isBanned" id="isBanned" {{ $user->isBanned ? 'checked' : '' }}>
                    <span class="slider round"></span>
                </label>
            </div>
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
