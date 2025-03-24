<x-layout>
    <x-addcard : link="adduser" object="User">

        <div style="display:flex; flex-direction:column; align-items:center;">
            <label for="user_name">
                User Name:
            </label>
            <input type="text" name="user_name" id="user_name" value="{{old('user_name')}}" autocomplete="off"
                style="height:20%; text-align:center; font-size:40%; width:fit-content; @error('user_name') border:2px solid red @enderror" required>
        </div>
        @error('user_name')
            <div class="error">{{ $message }}</div>
        @enderror

        <div style="display:flex; flex-direction:column; align-items:center;">
            <label for="user_number">
                User Number:
            </label>
            <div style="position: relative; width: fit-content; height:fit-content;">
                <input type="text" name="user_number" id="user_number" placeholder="9XXXXXXXX" value="{{old('user_number')}}" autocomplete="off"
                       inputmode="numeric" style="height: 20%; text-align: left; font-size: 40%;text-indent:30%; width: 100%; box-sizing: border-box; @error('user_number') border:2px solid red @enderror"
                       pattern="[0-9]{9}" required>
                <span style="position: absolute; left: 10px; top: 57.5%; transform: translateY(-50%); font-size: 50%; color: #000; pointer-events: none;">+963</span>
                <div style="position: absolute; left: 40px; top: 40%; height: 34%; width: 1px; background-color: #000;"></div>
            </div>
        </div>
        @error('user_number')
            <div class="error">{{ $message }}</div>
        @enderror
        <div style="display:flex; flex-direction:column; align-items:center;">
            <label for="user_password">
                User Password:
            </label>
            <input type="password" name="user_password" id="user_password" value=""
                style="height:20%; text-align:center; font-size:40%; width:fit-content;" minlength="8" required>
        </div>
        <br>

    </x-addcard>
</x-layout>
