<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Viewer</title>
</head>

<body>
    <h1>File Viewer</h1>

    @if (Str::startsWith($mimeType, 'video/'))
        <!-- Display video files -->
        <video width="640" height="360" controls>
            <source src="{{ asset('storage/files/' . $filename) }}" type="{{ $mimeType }}">
            Your browser does not support the video tag.
        </video>
    @elseif ($mimeType === 'application/pdf')
        <!-- Display PDF files -->
        <iframe src="{{ asset('storage/files/' . $filename) }}" width="100%" height="600px"
            style="border: none;"></iframe>
    @else
        <!-- Handle other file types (e.g., download link) -->
        <p>This file type cannot be displayed inline. <a href="{{ asset('storage/files/' . $filename) }}"
                download>Download the file</a>.</p>
    @endif
</body>

</html>
