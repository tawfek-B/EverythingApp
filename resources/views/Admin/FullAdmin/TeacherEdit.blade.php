@props(['teacher' => App\Models\Teacher::findOrFail(session('teacher'))])
<x-layout>

    @php
        $assignedObjects = $teacher->subjects->pluck('id')->toArray();
    @endphp
    <x-editcard :selectedSubjects="$assignedObjects" link="editteacher/{{ session('teacher') }}" relations="true" :subjects="$teacher->subjects"
        object="Teacher" menu="Subject" :menuModel="App\Models\Subject::all()" image="true" :objectModel=$teacher>
        <div>
            <div style="display:flex; flex-direction:column; align-items:center;">
                <label for="teacher_name" style="margin-bottom:10%;">
                    Teacher Name:
                </label>
                <input type="text" name="teacher_name" id="teacher_name" value="{{ $teacher->name }}"
                    style="height:20%; text-align:center; font-size:40%; width:fit-content; margin-bottom:10%;">
            </div>
            @error('teacher_name')
                <div class="error">{{ $message }}</div>
            @enderror
            <div style="display:flex; flex-direction:column; align-items:center; margin-bottom:10%;">
                <label for="teacher_user_name" style="margin-bottom:10%;">
                    Teacher User Name:
                </label>
                <input type="text" name="teacher_user_name" id="teacher_user_name" value="{{ $teacher->userName }}"
                    style="height:20%; text-align:center; font-size:40%; width:fit-content;">
            </div>
            @error('teacher_user_name')
                <div class="error">{{ $message }}</div>
            @enderror
            <div style="display:flex; flex-direction:column; align-items:center;">
                <label for="teacher_number">
                    Teacher Number:
                </label>
                <div style="position:relative; width: fit-content; height:fit-content; margin-bottom:10%;">
                    <input type="text" name="teacher_number" id="teacher_number" placeholder="9XXXXXXXX"
                        value="{{ $teacher->number }}" autocomplete="off" inputmode="numeric"
                        style="height: 20%; text-align: left; font-size: 40%;text-indent:30%; width: 100%; box-sizing: border-box; @error('teacher_number') border:2px solid red @enderror"
                        oninput="if (this.value.length > 9) this.value = this.value.slice(0, 9); this.value = this.value.replace(/(?!^)\+/g,'').replace(/[^0-9+]/g, '')"
                        pattern="[0-9]{9}" required>
                    <span
                        style="position: absolute; left: 10px; top: 57.5%; transform: translateY(-50%); font-size: 50%; color: #000; pointer-events: none;">+963</span>
                    <div
                        style="position: absolute; left: 40px; top: 40%; height: 34%; width: 1px; background-color: #000;">
                    </div>
                </div>
            </div>

            @error('teacher_number')
                <div class="error">{{ $message }}</div>
            @enderror
            @php
                $links = json_decode($teacher->links, true);
            @endphp
            <div style="display:flex; flex-direction:column; align-items:center; margin-bottom:10%;"
                name="teacher_links">
                <label for="teacher_name" style="margin-bottom:10%;">
                    Teacher Social Links:
                </label>
                <div style="display:grid; grid-template-columns: 1fr 1fr;gap:5%;">
                    <div class="input-container">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M2 6C2 3.79086 3.79086 2 6 2H18C20.2091 2 22 3.79086 22 6V18C22 20.2091 20.2091 22 18 22H6C3.79086 22 2 20.2091 2 18V6ZM6 4C4.89543 4 4 4.89543 4 6V18C4 19.1046 4.89543 20 6 20H12V13H11C10.4477 13 10 12.5523 10 12C10 11.4477 10.4477 11 11 11H12V9.5C12 7.567 13.567 6 15.5 6H16.1C16.6523 6 17.1 6.44772 17.1 7C17.1 7.55228 16.6523 8 16.1 8H15.5C14.6716 8 14 8.67157 14 9.5V11H16.1C16.6523 11 17.1 11.4477 17.1 12C17.1 12.5523 16.6523 13 16.1 13H14V20H18C19.1046 20 20 19.1046 20 18V6C20 4.89543 19.1046 4 18 4H6Z"
                                    fill="#000000"></path>
                            </g>
                        </svg>

                        <input type="url" value="{{ $links['Facebook'] }}"
                            style="height:20%; text-align:center; font-size:40%; width:fit-content;"
                            name="facebook_link" placeholder="Enter the Facebook link">
                        @error('facebook_link')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- <div class="input-container">
                        <!-- SVG Icon -->
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M12 18C15.3137 18 18 15.3137 18 12C18 8.68629 15.3137 6 12 6C8.68629 6 6 8.68629 6 12C6 15.3137 8.68629 18 12 18ZM12 16C14.2091 16 16 14.2091 16 12C16 9.79086 14.2091 8 12 8C9.79086 8 8 9.79086 8 12C8 14.2091 9.79086 16 12 16Z"
                                    fill="#0F0F0F"></path>
                                <path
                                    d="M18 5C17.4477 5 17 5.44772 17 6C17 6.55228 17.4477 7 18 7C18.5523 7 19 6.55228 19 6C19 5.44772 18.5523 5 18 5Z"
                                    fill="#0F0F0F"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M1.65396 4.27606C1 5.55953 1 7.23969 1 10.6V13.4C1 16.7603 1 18.4405 1.65396 19.7239C2.2292 20.8529 3.14708 21.7708 4.27606 22.346C5.55953 23 7.23969 23 10.6 23H13.4C16.7603 23 18.4405 23 19.7239 22.346C20.8529 21.7708 21.7708 20.8529 22.346 19.7239C23 18.4405 23 16.7603 23 13.4V10.6C23 7.23969 23 5.55953 22.346 4.27606C21.7708 3.14708 20.8529 2.2292 19.7239 1.65396C18.4405 1 16.7603 1 13.4 1H10.6C7.23969 1 5.55953 1 4.27606 1.65396C3.14708 2.2292 2.2292 3.14708 1.65396 4.27606ZM13.4 3H10.6C8.88684 3 7.72225 3.00156 6.82208 3.0751C5.94524 3.14674 5.49684 3.27659 5.18404 3.43597C4.43139 3.81947 3.81947 4.43139 3.43597 5.18404C3.27659 5.49684 3.14674 5.94524 3.0751 6.82208C3.00156 7.72225 3 8.88684 3 10.6V13.4C3 15.1132 3.00156 16.2777 3.0751 17.1779C3.14674 18.0548 3.27659 18.5032 3.43597 18.816C3.81947 19.5686 4.43139 20.1805 5.18404 20.564C5.49684 20.7234 5.94524 20.8533 6.82208 20.9249C7.72225 20.9984 8.88684 21 10.6 21H13.4C15.1132 21 16.2777 20.9984 17.1779 20.9249C18.0548 20.8533 18.5032 20.7234 18.816 20.564C19.5686 20.1805 20.1805 19.5686 20.564 18.816C20.7234 18.5032 20.8533 18.0548 20.9249 17.1779C20.9984 16.2777 21 15.1132 21 13.4V10.6C21 8.88684 20.9984 7.72225 20.9249 6.82208C20.8533 5.94524 20.7234 5.49684 20.564 5.18404C20.1805 4.43139 19.5686 3.81947 18.816 3.43597C18.5032 3.27659 18.0548 3.14674 17.1779 3.0751C16.2777 3.00156 15.1132 3 13.4 3Z"
                                    fill="#0F0F0F"></path>
                            </g>
                        </svg>
                        <input type="url" value="{{ $links['Instagram'] }}"
                            style="height:20%; text-align:center; font-size:40%; width:fit-content;"
                            name="instagram_link" placeholder="Enter the Instagram link">
                        @error('instagram_link')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div> --}}
                    <div class="input-container">
                        <!-- SVG Icon -->
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M23.1117 4.49449C23.4296 2.94472 21.9074 1.65683 20.4317 2.227L2.3425 9.21601C0.694517 9.85273 0.621087 12.1572 2.22518 12.8975L6.1645 14.7157L8.03849 21.2746C8.13583 21.6153 8.40618 21.8791 8.74917 21.968C9.09216 22.0568 9.45658 21.9576 9.70712 21.707L12.5938 18.8203L16.6375 21.8531C17.8113 22.7334 19.5019 22.0922 19.7967 20.6549L23.1117 4.49449ZM3.0633 11.0816L21.1525 4.0926L17.8375 20.2531L13.1 16.6999C12.7019 16.4013 12.1448 16.4409 11.7929 16.7928L10.5565 18.0292L10.928 15.9861L18.2071 8.70703C18.5614 8.35278 18.5988 7.79106 18.2947 7.39293C17.9906 6.99479 17.4389 6.88312 17.0039 7.13168L6.95124 12.876L3.0633 11.0816ZM8.17695 14.4791L8.78333 16.6015L9.01614 15.321C9.05253 15.1209 9.14908 14.9366 9.29291 14.7928L11.5128 12.573L8.17695 14.4791Z"
                                    fill="#0F0F0F"></path>
                            </g>
                        </svg>

                        <input type="url" value="{{ $links['Telegram'] }}"
                            style="height:20%; text-align:center; font-size:40%; width:fit-content;"
                            name="telegram_link" placeholder="Enter the Telegram link">
                        @error('telegram_link')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div style="display:grid; grid-template-columns: 1fr;gap:5%;">
                    <div class="input-container">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M9.49614 7.13176C9.18664 6.9549 8.80639 6.95617 8.49807 7.13509C8.18976 7.31401 8 7.64353 8 8V16C8 16.3565 8.18976 16.686 8.49807 16.8649C8.80639 17.0438 9.18664 17.0451 9.49614 16.8682L16.4961 12.8682C16.8077 12.6902 17 12.3589 17 12C17 11.6411 16.8077 11.3098 16.4961 11.1318L9.49614 7.13176ZM13.9844 12L10 14.2768V9.72318L13.9844 12Z"
                                    fill="#0F0F0F"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M0 12C0 8.25027 0 6.3754 0.954915 5.06107C1.26331 4.6366 1.6366 4.26331 2.06107 3.95491C3.3754 3 5.25027 3 9 3H15C18.7497 3 20.6246 3 21.9389 3.95491C22.3634 4.26331 22.7367 4.6366 23.0451 5.06107C24 6.3754 24 8.25027 24 12C24 15.7497 24 17.6246 23.0451 18.9389C22.7367 19.3634 22.3634 19.7367 21.9389 20.0451C20.6246 21 18.7497 21 15 21H9C5.25027 21 3.3754 21 2.06107 20.0451C1.6366 19.7367 1.26331 19.3634 0.954915 18.9389C0 17.6246 0 15.7497 0 12ZM9 5H15C16.9194 5 18.1983 5.00275 19.1673 5.10773C20.0989 5.20866 20.504 5.38448 20.7634 5.57295C21.018 5.75799 21.242 5.98196 21.4271 6.23664C21.6155 6.49605 21.7913 6.90113 21.8923 7.83269C21.9973 8.80167 22 10.0806 22 12C22 13.9194 21.9973 15.1983 21.8923 16.1673C21.7913 17.0989 21.6155 17.504 21.4271 17.7634C21.242 18.018 21.018 18.242 20.7634 18.4271C20.504 18.6155 20.0989 18.7913 19.1673 18.8923C18.1983 18.9973 16.9194 19 15 19H9C7.08058 19 5.80167 18.9973 4.83269 18.8923C3.90113 18.7913 3.49605 18.6155 3.23664 18.4271C2.98196 18.242 2.75799 18.018 2.57295 17.7634C2.38448 17.504 2.20866 17.0989 2.10773 16.1673C2.00275 15.1983 2 13.9194 2 12C2 10.0806 2.00275 8.80167 2.10773 7.83269C2.20866 6.90113 2.38448 6.49605 2.57295 6.23664C2.75799 5.98196 2.98196 5.75799 3.23664 5.57295C3.49605 5.38448 3.90113 5.20866 4.83269 5.10773C5.80167 5.00275 7.08058 5 9 5Z"
                                    fill="#0F0F0F"></path>
                            </g>
                        </svg>

                        <input type="url" value="{{ $links['YouTube'] }}"
                            style="height:20%; text-align:center; font-size:40%; width:fit-content;"
                            name="youtube_link" placeholder="Enter the YouTube link">
                        @error('youtube_link')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div>
                <label for="selected_objects">
                    Subjects:<br>
                    (Click to remove and re-add)
                </label>
                <br>
            </div>

    </x-editcard>
    </div>
</x-layout>
</body>
