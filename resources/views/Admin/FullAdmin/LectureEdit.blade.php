@props(['lecture' => App\Models\Lecture::findOrFail(session('lecture'))])
<x-layout>
    <x-editcard : link="editlecture/{{ session('lecture') }}" object="Lecture" :objectModel=$lecture :image=true>
        <div>
            <div style="display:flex; flex-direction:column; align-items:center;">
                <label for="lecture_name">
                    Lecture Name:
                </label>
                <input type="text" name="lecture_name" id="lecture_name" value="{{ $lecture->name }}"
                    style="height:20%; text-align:center; font-size:40%; width:fit-content;" />
            </div>
            <br>

            {{-- <div style="display:flex; flex-direction:column; align-items:center;">
                <label for="lecture_description">
                    Lecture Description:
                </label>

                <textarea name="lecture_description" id="lecture_description" autocomplete="off"
                    style="height:150px; width:80%; font-size:16px; padding:10px; resize:vertical;" required>{{ $lecture->description }}</textarea>
            </div>
            <br> --}}

            <span>Video File:</span>
            <br>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px">
                <div>
                    <label for="actual-file-input-360">360p</label>
                    <div class="custom-file-input">
                        <input type="file" id="actual-file-input-360" class="hidden-file-input"
                            name="lecture_file_360" accept="video/*" @if ($lecture->file_360 != null) disabled @endif>
                        <label for="actual-file-input-360"
                            class="file-input-label"@if ($lecture->file_360 != null) disabled @endif>
                            <span class="file-input-text" id="file-input-text-360">Choose a file @if ($lecture->file_360 != null)
                                    <br> (FILE ALREADY UPLOADED)
                                @endif
                            </span>
                        </label>
                    </div>
                </div>
                <div>
                    <label for="actual-file-input-720">720p</label>
                    <div class="custom-file-input">
                        <input type="file" id="actual-file-input-720" class="hidden-file-input"
                            name="lecture_file_720" accept="video/*" @if ($lecture->file_720 != null) disabled @endif>
                        <label for="actual-file-input-720" class="file-input-label"
                            @if ($lecture->file_720 != null) disabled @endif>
                            <span class="file-input-text" id="file-input-text-720">Choose a file @if ($lecture->file_720 != null)
                                    <br> (FILE ALREADY UPLOADED)
                                @endif
                            </span>
                        </label>
                    </div>
                </div>
            </div>
            <div style="display: flex; flex-direction:row;">
                <div style="margin-left:auto;margin-right:auto;">

                    <label for="actual-file-input-1080">1080p</label>
                    <div class="custom-file-input">
                        <input type="file" id="actual-file-input-1080" class="hidden-file-input"
                            name="lecture_file_1080" accept="video/*" @if ($lecture->file_1080 != null) disabled @endif>
                        <label for="actual-file-input-1080" class="file-input-label"
                            @if ($lecture->file_1080 != null) disabled @endif>
                            <span class="file-input-text" id="file-input-text-1080">Choose a file @if ($lecture->file_1080 != null)
                                    <br> (FILE ALREADY UPLOADED)
                                @endif
                            </span>
                        </label>
                    </div>
                </div>
            </div>
            <br>

    </x-editcard>
    </div>
</x-layout>
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
        });
    }

    // Set up all file inputs
    setupFileInput('actual-file-input-360', 'file-input-text-360');
    setupFileInput('actual-file-input-720', 'file-input-text-720');
    setupFileInput('actual-file-input-1080', 'file-input-text-1080');
</script>
