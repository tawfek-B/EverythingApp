@props(['subjectID' => null])
<x-layout>
    <x-addcard link="addlecture" object="Lecture">
        <div style="display:flex; flex-direction:column; align-items:center;">
            <label for="lecture_name">
                Lecture Name:
            </label>
            <input type="text" name="lecture_name" id="lecture_name" value="" autocomplete="off"
                style="height:20%; text-align:center; font-size:40%; width:fit-content;" required>
        </div>
        {{-- <div style="display:flex; flex-direction:column; align-items:center; height:100%;">
            <label for="lecture_description">
                Lecture Description (optional):
            </label>
            <textarea name="lecture_description" id="lecture_description" autocomplete="off"
                style="height:150px; width:80%; font-size:16px; padding:10px; resize:vertical;max-height:500px;"></textarea>
        </div>
        --}}
        <br>
        <label for="subject">
            Subject: <br>
        </label>

        <select name="subject" id="subject" required>
            <option value="" selected>Select Subject</option>
            @foreach (App\Models\Teacher::findOrFail(Auth::user()->teacher_id)->subjects as $subject)
                @if ($subjectID != null && $subjectID == $subject->id)
                    <option value="{{ $subject->id }}" selected>{{ $subject->name }}</option>
                @else
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endif
            @endforeach
        </select>
        <br>
        <br>
        <span>Video File (upload at least one):</span>
        <br>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px">
            <div>
                <label for="actual-file-input-360">360p</label>
                <div class="custom-file-input">
                    <input type="file" id="actual-file-input-360" class="hidden-file-input" name="lecture_file_360"
                        accept="video/*">
                    <label for="actual-file-input-360" class="file-input-label">
                        <span class="file-input-text" id="file-input-text-360">Choose a file</span>
                    </label>
                </div>
            </div>
            <div>
                <label for="actual-file-input-720">720p</label>
                <div class="custom-file-input">
                    <input type="file" id="actual-file-input-720" class="hidden-file-input" name="lecture_file_720"
                        accept="video/*">
                    <label for="actual-file-input-720" class="file-input-label">
                        <span class="file-input-text" id="file-input-text-720">Choose a file</span>
                    </label>
                </div>
            </div>
        </div>
        <div style="display: flex; flex-direction:row;">
            <div style="margin-left:auto;margin-right:auto;">

                <label for="actual-file-input-1080">1080p</label>
                <div class="custom-file-input">
                    <input type="file" id="actual-file-input-1080" class="hidden-file-input" name="lecture_file_1080"
                        accept="video/*">
                    <label for="actual-file-input-1080" class="file-input-label">
                        <span class="file-input-text" id="file-input-text-1080">Choose a file</span>
                    </label>
                </div>
            </div>
        </div>
        <br>
        <div id="file-error" style="color: red; display: none; text-align: center;">
            Please upload at least one video file (360p, 720p, or 1080p)
        </div>
        <br>
    </x-addcard>

    <script>
        // Function to handle file input changes
        function setupFileInput(inputId, textId) {
            const input = document.getElementById(inputId);
            const textElement = document.getElementById(textId);

            input.addEventListener('change', function(event) {
                const file = event.target.files[0];

                if (file) {
                    // Check file type
                    const allowedTypes = ['video'];
                    const isAllowed = allowedTypes.some(type => file.type.startsWith(type));

                    if (!isAllowed) {
                        alert('Invalid file type. Please upload a video file.');
                        event.target.value = '';
                        textElement.textContent = 'Choose a file';
                        return;
                    }

                    // Update the display text
                    textElement.textContent = file.name;
                } else {
                    textElement.textContent = 'Choose a file';
                }

                // Hide error message when a file is selected
                document.getElementById('file-error').style.display = 'none';
            });
        }

        // Form validation function
        function validateLectureForm() {
            const file360 = document.getElementById('actual-file-input-360').files.length;
            const file720 = document.getElementById('actual-file-input-720').files.length;
            const file1080 = document.getElementById('actual-file-input-1080').files.length;

            if (!file360 && !file720 && !file1080) {
                document.getElementById('file-error').style.display = 'block';

                // Scroll to error message
                document.getElementById('file-error').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                return false; // Prevent form submission
            }

            return true; // Allow form submission
        }

        // Set up all file inputs when page loads
        document.addEventListener('DOMContentLoaded', function() {
            setupFileInput('actual-file-input-360', 'file-input-text-360');
            setupFileInput('actual-file-input-720', 'file-input-text-720');
            setupFileInput('actual-file-input-1080', 'file-input-text-1080');
        });
    </script>
</x-layout>
