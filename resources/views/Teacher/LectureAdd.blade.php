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
        <div style="display:flex; flex-direction:column; align-items:center; height:100%;">
            <label for="lecture_description">
                Lecture Description (optional):
            </label>
            <textarea name="lecture_description" id="lecture_description" autocomplete="off"
                style="height:150px; width:80%; font-size:16px; padding:10px; resize:vertical;max-height:500px;"></textarea>
        </div>

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
        <label for="actual-file-input">
            Video File:
        </label>
        <br>
        <div class="custom-file-input">
            <input type="file" id="actual-file-input" class="hidden-file-input" name="lecture_file" accept="video/*" required>
            <label for="actual-file-input" class="file-input-label">
                <span class="file-input-text">Choose a file</span>
            </label>
        </div>
        <br>

    </x-addcard>

    <script>
        document.getElementById('actual-file-input').addEventListener('change', function(event) {
            const file = event.target.files[0]; // Get the selected file
            const allowedTypes = ['video', 'audio', 'application/pdf']; // Allowed MIME types
            const fileType = file.type; // Get the MIME type of the file

            // Check if the file type is allowed
            const isAllowed = allowedTypes.some(type => fileType.startsWith(type));

            if (!isAllowed) {
                alert('Invalid file type. Please upload a video, audio, or PDF file.');
                event.target.value = ''; // Clear the file input
            }
        });
        document.querySelector('.hidden-file-input').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
            document.querySelector('.file-input-text').setAttribute('data-file', fileName);
        });
    </script>
</x-layout>
