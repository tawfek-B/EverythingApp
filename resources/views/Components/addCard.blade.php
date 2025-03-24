@props([
    'link' => '#',
    'object' => null,
    'image' => null,
])

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
        width: 30%;
        height: auto;
        display: flex;
        flex-direction: column;
        border: black 5px solid;
        align-items: center;
        background: #6699CC;
        justify-content: center;
        border-radius: 15px;
        margin-bottom: 0;
        padding: 20px;
    }

    .textContainer {
        line-height: 50px;
        z-index: 2;
        font-size: 30px;
        text-align: center;
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
        transition: 0.3s;
    }

    .submit-button:hover {
        background: white;
        color: black;
    }

    .submit-button:disabled:hover {
        background-color: white;
        color: darkgray;
        border-color: darkgray;
        cursor: not-allowed;
    }

    .submit-button:disabled {
        background-color: white;
        color: darkgray;
        border-color: darkgray;
        cursor: not-allowed;
    }

    .image {
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
</style>



<div class="ObjectContainer">
    <form action="/{{ $link }}" method="POST" style="display:flex;flex-direction:column"
        enctype="multipart/form-data">
        @csrf
        <div style="width:50%; height:10%; margin-left:auto; margin-right:auto">
            @if ($object == 'Teacher')
                <img src="{{ asset('Admins/teacherDefault.png') }}" alt="" id="image_preview"
                    class="image"accept="image/*" onchange="validateImageSize(this)">
            @elseif ($object == 'Admin')
                <img src="{{ asset('Admins/adminDefault.png') }}" alt="" id="image_preview"
                    class="image"accept="image/*" onchange="validateImageSize(this)">
            @else
                <img src="{{ asset($object . 's/default.png') }}" alt="" id="image_preview"
                    class="image"accept="image/*" onchange="validateImageSize(this)">
            @endif
        </div>
        <div
            style="display:flex; flex-direction:column; align-items:center; margin-top:5%;margin-bottom:5%; font-size:2rem;">
            <label for="object_image">
                {{ $object }} Image:
            </label>

            <input type="file" name="object_image" id="object_image"
                placeholder="Enter the image of the {{ Str::lower($object) }}" accept="image/*"
                onchange="validateImageSize(this)">
            <label for="" style="color:#333333; font-size:2rem; text-align:center">Make sure the size is less
                than
                2MB.<br>Recomended, 1:1 aspect ratio</label>
        </div>
        @error('object_image')
            <div class="error">{{ $message }}</div>
        @enderror
        <br>
        <div class="textContainer">
            {{ $slot }}
        </div>

        <button type="submit" class="submit-button">Add {{ $object }}</button>
    </form>
    <script>
        function validateImageSize(input) {
            const maxSize = 2 * 1024 * 1024; // 2MB in bytes
            if (input.files && input.files[0]) {
                const fileSize = input.files[0].size;
                if (fileSize > maxSize) {
                    alert('Image size must be less than 2MB.');
                    input.value = ''; // Clear the file input
                }
            }
        }
    </script>
    @php

        $model = 'App\Models\\' . $object;
        $imagePath = asset($object . 's/default.png');

        if ($object == 'Admin' || $object == 'Teacher') {
            $imagePath = asset($object . 's/' . $object . 'Default.png');
        }
    @endphp
    <script>
        const imageInput = document.getElementById('object_image');
        const imagePreview = document.getElementById('image_preview');

        imageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result; // Update preview with selected image
                };
                reader.readAsDataURL(file);
            } else {
                // Reset to the original image if no file is selected

                imagePreview.src =
                    $imagePath;
            }
        });
    </script>
