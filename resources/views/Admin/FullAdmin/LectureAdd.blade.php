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
            @foreach (App\Models\Subject::all() as $subject)
                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
            @endforeach
        </select>
        <br>
        <br>
        <label for="lecture_file">
            File (Video, PDF, or Audio):
            <br>
        </label>
        <input type="file" name="lecture_file" id="lecture_file" accept="video/*, audio/*, application/pdf" required>
    </x-addcard>

    <!-- JavaScript for file validation -->
    <script>
        document.getElementById('lecture_file').addEventListener('change', function (event) {
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
    </script>
</x-layout>