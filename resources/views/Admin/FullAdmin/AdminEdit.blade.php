@props(['admin' => App\Models\Admin::findOrFail(session('admin'))])
<x-layout>
    <x-editcard : link="editadmin/{{ session('admin') }}" object="Admin" :image=true :objectModel=$admin>
        <div>
            <div style="display:flex; flex-direction:column; align-items:center;">
                <label for="admin_name" style="margin-bottom:10%;">
                    Admin Name:
                </label>
                <input type="text" name="admin_name" id="admin_name" value="{{ $admin->name }}"
                    style="height:20%; text-align:center; font-size:40%; width:fit-content; margin-bottom:10%;">
            </div>
            @error('admin_name')
                <div class="error">{{ $message }}</div>
            @enderror
            <div style="display:flex; flex-direction:column; align-items:center;">
                <label for="admin_user_name" style="margin-bottom:10%;">
                    Admin User Name:
                </label>
                <input type="text" name="admin_user_name" id="admin_user_name" value="{{ $admin->userName }}"
                    style="height:20%; text-align:center; font-size:40%; width:fit-content; margin-bottom:10%;">
            </div>
            @error('admin_user_name')
                <div class="error">{{ $message }}</div>
            @enderror
            <div style="display:flex; flex-direction:column; align-items:center;">
                <label for="admin_number">
                    Admin Number:
                </label>
                <div style="position:relative; width: fit-content; height:fit-content; margin-bottom:10%;">
                    <input type="text" name="admin_number" id="admin_number" placeholder="9XXXXXXXX"
                        value="{{ $admin->number }}" autocomplete="off" inputmode="numeric"
                        style="height: 20%; text-align: left; font-size: 40%;text-indent:30%; width: 100%; box-sizing: border-box; @error('admin_number') border:2px solid red @enderror"
                        oninput="if (this.value.length > 9) this.value = this.value.slice(0, 9); this.value = this.value.replace(/(?!^)\+/g,'').replace(/[^0-9+]/g, '')"
                        pattern="[0-9]{9}" required>
                    <span
                        style="position: absolute; left: 10px; top: 57.5%; transform: translateY(-50%); font-size: 50%; color: #000; pointer-events: none;">+963</span>
                    <div
                        style="position: absolute; left: 40px; top: 40%; height: 34%; width: 1px; background-color: #000;">
                    </div>
                </div>
            </div>

            @error('admin_number')
                <div class="error">{{ $message }}</div>
            @enderror
            <div class="dropdown-container">
                <label for="admin-dropdown" style="font-size: 30px;">Privileges:</label>
                <select id="admin-dropdown" name="admin_privileges" class="dropdown" style="padding:1rem 1.5rem; text-align:left">
                    @if ($admin->privileges == 1)
                        <option selected>Semi-Admin</option>
                        <option>Admin</option>
                    @elseif($admin->privileges == 2)
                        <option>Semi-Admin</option>
                        <option selected>Admin</option>
                    @endif

                </select>
            </div>

    </x-editcard>
    </div>
</x-layout>
