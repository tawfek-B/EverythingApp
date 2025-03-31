@props([
    'relations' => false,
    'link' => '#',
    'object' => null,
    'selectedSubjects' => null,
    'subjects' => null,
    'menu' => null,
    'menuModel' => null,
    'image' => false,
    'objectModel' => null,
    'model' => null,
    'lectures' => false,
    'subscribedLectureIds' => null,
])
@if ($lectures != false)
    @php
        $subscribedLectureIds = $model->lectures->pluck('id')->toArray();
        $selectedLectures = $model->lectures->pluck('id')->toArray();
    @endphp
@endif
<style>
    .input-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 50%;
        grid-row-gap: 10%;
    }

    .icon {
        width: 80%;
        /* Adjust the size of the SVG icon */
        height: 80%;
        /* Adjust the size of the SVG icon */
        cursor: pointer;
        /* Optional: Add a pointer cursor for interactivity */
        transition: transform 0.3s ease;
    }

    .icon:hover {
        transform: scale(1.1);
        /* Slightly enlarge the icon on hover */
        transition: transform 0.3s ease;
        /* Smooth transition */
    }

    .ObjectContainer {
        width: 40rem;
        height: auto;
        display: flex;
        flex-direction: column;
        border: black 5px solid;
        align-items: center;
        justify-content: center;
        border-radius: 15px;
        margin-bottom: 0;
        padding: 20px;
        background-color: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    .textContainer {
        line-height: 50px;
        z-index: 2;
        font-size: 30px;
        text-align: center;
    }

    .subject-button {
        background: #193E6C;
        padding: 5px 15px;
        font-size: 16px;
        border: none;
        color: #193E6C;
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s ease;
    }

    .subject-button.selected {
        background-color: #193E6C;
        color: white;
    }

    .lecture-button {
        background: #193E6C;
        padding: 5px 15px;
        font-size: 16px;
        border: none;
        color: #193E6C;
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s ease;
    }

    .lecture-button.selected {
        background-color: #193E6C;
        color: white;
    }

    .submit-button {
        margin-top: 20px;
        margin-right: auto;
        margin-left: auto;
        padding: 10px 20px;
        font-size: 18px;
        background: #6699CC;
        border: #193E6C 3px solid;
        color: white;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .submit-button:hover:not(:disabled) {
        background: white;
        color: black;
        animation: pulse 1s infinite;
    }

    .submit-button:disabled:hover,
    .submit-button:disabled {
        background-color: white;
        color: darkgray;
        border-color: darkgray;
        cursor: not-allowed;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(40, 40, 40, 0.7);
        }

        50% {
            box-shadow: 0 0 0 8px rgba(40, 40, 40, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(40, 40, 40, 0);
        }
    }

    .dropdown-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        margin-bottom: 20px;
        gap: 5px;
    }

    .dropdown {
        padding: 5px;
        font-size: 16px;
        position: relative;
        display: inline-block;
        margin-right: auto;
        margin-left: auto;
    }

    .dropbtn {
        background-color: #6699CC;
        color: black;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 20px;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1;
        grid-template-columns: auto auto;
        gap: 10px;
        bottom: 100%;
        /* Add this line to position the dropdown above the button */
        top: auto;
        /* Add this line to override the default top positioning */
    }

    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        position: relative;
    }

    .dropdown-content a:hover {
        background-color: #f1f1f1;
    }

    .nested-dropdown {
        display: none;
        position: absolute;
        left: 100%;
        top: 0;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1;
    }

    .dropdown-content a:hover+.nested-dropdown,
    .nested-dropdown:hover {
        display: block;
    }

    .subject-item {
        padding: 0.1rem 0.1rem;
    }

    .subject-item:hover .nested-dropdown {
        display: block;
    }

    .add-subject-btn {
        padding: 5px 10px;
        font-size: 16px;
        background: #193E6C;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        width: fit-content;
        text-align: center;
    }

    .image {
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
</style>

<div class="ObjectContainer">
    <form action="/{{ $link }}" method="POST" enctype="multipart/form-data"
        onsubmit="@if ($relations) updateHiddenInput(); @endif return confirmEdit();"
        style="display:flex;flex-direction:column">
        @csrf
        @method('PUT')
        @if ($image)
            <div style="width:50%; height:10%; margin-left:auto; margin-right:auto">
                <img src="{{ asset($objectModel->image) }}" alt="" id="image_preview" class="image">
            </div>
            <div
                style="display:flex; flex-direction:column; align-items:center; margin-top:5%;margin-bottom:5%; font-size:2rem;">
                <label for="object_image">{{ $object }} Image:</label>
                <input type="file" name="object_image" id="object_image"
                    placeholder="Enter the image of the {{ Str::lower($object) }}" accept="image/*"
                    onchange="validateImageSize(this)">
                <label for="object_image" style="color:#333333; font-size:2rem; text-align:center">Make sure the size is
                    less than 2MB.<br>Recomended, 1:1 aspect ratio</label>
            </div>
            @error('object_image')
                <div class="error">{{ $message }}</div>
            @enderror
            <br>
        @endif
        <div class="textContainer">{{ $slot }}</div>
        @if ($relations)
            <div id="subject-buttons-container" class="buttonContainer">
                @foreach ($subjects as $subject)
                    <button type="button" class="subject-button selected"
                        data-subject-id="{{ $subject->id }}">{{ $subject->name }}</button>
                @endforeach
            </div>
            <br>
            <div class="dropdown-container">
                <label for="subject-dropdown" style="font-size: 30px;">Add {{ $menu }}:</label>
                <select id="subject-dropdown" class="dropdown" style="padding:0.5rem 2.5rem">
                    <option value="">Select a {{ $menu }}</option>
                    @foreach ($menuModel as $subject)
                        @if (!in_array($subject->id, $selectedSubjects))
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endif
                    @endforeach
                </select>
                <input type="button" id="add-subject-btn" class="add-subject-btn" value="Add {{ $menu }}">
            </div>
            <input type="hidden" name="selected_objects" id="selected_objects_input">
        @endif
        @if ($lectures != false)
            <label for="selected_lectures">
                Lectures:<br>
                (Click to remove and re-add)
            </label>
            <br>
            <div id="subscribed-lectures-container" class="buttonContainer">
                @foreach ($model->lectures->pluck('id')->toArray() as $lecture)
                    <button type="button" class="lecture-button selected" data-lecture-id="{{ $lecture }}"
                        onclick="toggleLectureSelection(this)">{{ App\Models\Lecture::findOrFail($lecture)->name }}</button>
                @endforeach
            </div>
            <div class="dropdown" id="lectureD">
                <button class="dropbtn" onclick="toggleDropdown(event)">Select Lecture</button>
                <div class="dropdown-content" id="lectureDropdown">
                    @foreach (App\Models\Subject::all() as $subject)
                        @if (!in_array($subject->id, $model->subjects->pluck('id')->toArray()))
                            <div class="subject-item">
                                <a>{{ $subject->name }} >
                                    <div class="nested-dropdown">
                                        @if ($subject->lectures->isEmpty())
                                            <div style="padding:0.25rem 0.25rem; background-color:darkgray">No lectures
                                                for {{ $subject->name }}</div>
                                        @else
                                            @foreach ($subject->lectures as $lecture)
                                                @if (!in_array($lecture->id, $model->lectures->pluck('id')->toArray()))
                                                    <div data-lecture-id="{{ $lecture->id }}"
                                                        style="padding:0.25rem 0.25rem; cursor:pointer"
                                                        onclick="selectLecture(this)">
                                                        {{ $lecture->name }}
                                                    </div>
                                                @else
                                                    <div
                                                        style="padding:0.25rem 0.25rem; cursor:pointer; color:#333333; cursor:default; background-color:darkgray">
                                                        {{ $lecture->name }}
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </a>
                            </div>
                        @else
                            <div class="subject-item" style="background-color:darkgray; line-height:2.5rem">
                                {{ $subject->name }} <br> (Already Subscribed)
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <input type="hidden" name="selected_lectures" id="selected_lectures_input">
        @endif
        <br>
        <button type="submit" class="submit-button">Update {{ $object }}</button>
    </form>
</div>

<script>
    function validateImageSize(input) {
        const maxSize = 2 * 1024 * 1024;
        if (input.files && input.files[0]) {
            const fileSize = input.files[0].size;
            if (fileSize > maxSize) {
                alert('Image size must be less than 2MB.');
                input.value = '';
            }
        }
    }
</script>
@php
    $model = 'App\Models\\' . $object;
    $imagePath = asset($model::where('id', session(Str::lower($object)))->first()->image);
@endphp
<script>
    const imageInput = document.getElementById('object_image');
    const imagePreview = document.getElementById('image_preview');
    if (@json($image) != false) {
        imageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.src = $imagePath;
            }
        });
    }
</script>
@if ($relations)
    <script>
        let initialValues = {};
        let submitButton = document.querySelector(".submit-button");
        document.querySelectorAll(
            "input[type='text'], input[type='password'], input[type='file'], input[type='url'], textarea").forEach(
            input => {
                initialValues[input.name] = input.value;
            });

        function checkForChanges() {
            let hasChanged = false;
            document.querySelectorAll(
                "input[type='text'], input[type='password'], input[type='file'], input[type='url'], textarea").forEach(
                input => {
                    if (input.value !== initialValues[input.name]) hasChanged = true;
                });
            let initialSubjectsSet = new Set(@json($selectedSubjects).map(String));
            let selectedSubjectsSet = new Set([...selectedSubjects].map(String));
            if (!setsAreEqual(initialSubjectsSet, selectedSubjectsSet)) hasChanged = true;
            @if ($lectures != false)
                let initialLecturesSet = new Set(@json($selectedLectures).map(String));
                let selectedLecturesSet = new Set([...selectedLectures].map(String));
                if (!setsAreEqual(initialLecturesSet, selectedLecturesSet)) hasChanged = true;
            @endif
            submitButton.disabled = !hasChanged;
        }

        function setsAreEqual(setA, setB) {
            if (setA.size !== setB.size) return false;
            for (let item of setA)
                if (!setB.has(item)) return false;
            return true;
        }

        let selectedSubjects = new Set();
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".subject-button").forEach(button => {
                let subjectId = button.getAttribute("data-subject-id");
                selectedSubjects.add(subjectId);
                button.addEventListener("click", function() {
                    toggleSubjectSelection(this, subjectId);
                });
            });

            document.getElementById('add-subject-btn').addEventListener('click', function() {
                let dropdown = document.getElementById('subject-dropdown');
                let selectedSubjectId = dropdown.value;
                let selectedSubjectName = dropdown.options[dropdown.selectedIndex].text;
                if (!selectedSubjectId || selectedSubjects.has(selectedSubjectId)) return;
                let buttonContainer = document.getElementById('subject-buttons-container');
                let newButton = document.createElement('button');
                newButton.type = "button";
                newButton.classList.add('subject-button', 'selected');
                newButton.setAttribute('data-subject-id', selectedSubjectId);
                newButton.textContent = selectedSubjectName;
                newButton.style.backgroundColor = "#193E6C";
                newButton.style.color = "";
                newButton.addEventListener('click', function() {
                    toggleSubjectSelection(this, selectedSubjectId);
                });
                buttonContainer.appendChild(newButton);
                selectedSubjects.add(selectedSubjectId);
                checkForChanges();
            });
        });

        function toggleSubjectSelection(button, subjectId) {
            if (selectedSubjects.has(subjectId)) {
                selectedSubjects.delete(subjectId);
                button.classList.remove("selected");
                button.style.backgroundColor = "";
                button.style.color = "#193E6C";
            } else {
                selectedSubjects.add(subjectId);
                button.classList.add("selected");
                button.style.backgroundColor = "#193E6C";
                button.style.color = "#FFFFFF";
            }
            checkForChanges();
        }

        function updateHiddenInput() {
            document.getElementById('selected_objects_input').value = JSON.stringify(Array.from(selectedSubjects));
        }
        document.querySelectorAll(
            "input[type='text'], input[type='password'], input[type='file'], input[type='url'], textarea").forEach(
            input => {
                input.addEventListener("input", checkForChanges);
            });
        submitButton.disabled = true;
    </script>
@else
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let submitButton = document.querySelector(".submit-button");
            let form = document.querySelector("form");
            let initialValues = {};
            document.querySelectorAll(
                "input[type='text'], input[type='password'], input[type='file'], input[type='url'], select, textarea"
            ).forEach(
                input => {
                    initialValues[input.name] = input.value;
                });

            function checkForChanges() {
                let hasChanged = false;
                document.querySelectorAll(
                    "input[type='text'], input[type='password'], input[type='file'], input[type='url'], select, textarea"
                ).forEach(
                    input => {
                        if (input.value !== initialValues[input.name]) hasChanged = true;
                    });
                submitButton.disabled = !hasChanged;
            }

            document.querySelectorAll(
                "input[type='text'], input[type='password'], input[type='file'], select, textarea").forEach(
                input => {
                    input.addEventListener("input", checkForChanges);
                    input.addEventListener("change", checkForChanges);
                });
            submitButton.disabled = true;
        });
    </script>
@endif
<script>
    const object = @json($object);
    const authID = @json(auth()->id());
    const sessionID = @json(session('admin'));

    function confirmEdit() {
        if (object === "Admin" && authID == sessionID) return confirm(
            "Changing your info will require logging out.\n\nAre you sure you want to proceed?");
    }
</script>
@if ($lectures != false)
    <script>
        let selectedLectures = new Set(@json($subscribedLectureIds).map(id => parseInt(id)));

        function toggleLectureSelection(button) {
            const lectureId = parseInt(button.getAttribute('data-lecture-id'));
            if (selectedLectures.has(lectureId)) {
                selectedLectures.delete(lectureId);
                button.classList.remove("selected");
                button.style.backgroundColor = "";
                button.style.color = "#193E6C";
            } else {
                selectedLectures.add(lectureId);
                button.classList.add("selected");
                button.style.backgroundColor = "#193E6C";
                button.style.color = "#FFFFFF";
            }
            document.getElementById('selected_lectures_input').value = JSON.stringify(Array.from(selectedLectures));
            checkForChanges();
        }

        function addLectureButton(lectureId, lectureName) {
            const buttonContainer = document.getElementById('subscribed-lectures-container');
            const newButton = document.createElement('button');
            newButton.type = "button";
            newButton.classList.add('lecture-button', 'selected');
            newButton.setAttribute('data-lecture-id', lectureId);
            newButton.textContent = lectureName;
            newButton.addEventListener('click', function() {
                toggleLectureSelection(this);
            });
            buttonContainer.appendChild(newButton);
            checkForChanges();
        }

        function removeLectureButton(lectureId) {
            const button = document.querySelector(`.lecture-button[data-lecture-id="${lectureId}"]`);
            if (button) button.remove();
            checkForChanges();
        }

        function selectLecture(element) {
            const lectureId = parseInt(element.getAttribute('data-lecture-id'));
            if (selectedLectures.has(lectureId)) {

                // selectedLectures.delete(lectureId);
                // element.style.backgroundColor = '';
                // element.style.color = '';
                // removeLectureButton(lectureId);

            } else {
                selectedLectures.add(lectureId);
                element.style.backgroundColor = '';
                element.style.color = '';
                addLectureButton(lectureId, element.textContent);
            }
            document.getElementById('selected_lectures_input').value = JSON.stringify(Array.from(selectedLectures));
            checkForChanges();
        }
        // Function to toggle the dropdown
        function toggleDropdown(event) {
            event.preventDefault(); // Prevent the button from submitting the form
            const dropdownContent = event.target.nextElementSibling;
            if (dropdownContent && dropdownContent.classList.contains('dropdown-content')) {
                dropdownContent.style.display = dropdownContent.style.display === "block" ? "none" : "block";
            }
        }

        // Close the dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('lectureD');
            const dropdownContent = document.getElementById('lectureDropdown');

            // Check if the click is outside the dropdown
            if (!dropdown.contains(event.target)) {
                dropdownContent.style.display = 'none'; // Hide the dropdown
            }
        });
    </script>
@endif
