@props(['objects' => false, 'object', 'nav' => 'true'])
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pridi:wght@200;300;400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="icon" href="{{ asset('storage/Web/favicon.ico') }}" sizes="any">

    <link rel="icon" href="{{ asset('storage/Web/favicon.svg') }}" type="image/svg+xml">

    <link rel="icon" href="{{ asset('storage/Web/favicon-32x32.png') }}" type="image/png" sizes="32x32">
    <link rel="icon" href="{{ asset('storage/Web/favicon-16x16.png') }}" type="image/png" sizes="16x16">

    <link rel="apple-touch-icon" href="{{ asset('Web/apple-touch-icon.png') }}" sizes="180x180">

    {{-- <link rel="manifest" href="{{ asset('site.webmanifest') }}"> --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Everything</title>

    <style>
        html {
            font-size: 11px;
        }

        /* Responsive font scaling */
        @media (max-width: 1200px) {
            html {
                font-size: 10px;
            }
        }

        @media (max-width: 992px) {
            html {
                font-size: 9px;
            }
        }

        @media (max-width: 768px) {
            html {
                font-size: 8px;
            }
        }

        @media (max-width: 480px) {
            html {
                font-size: 7px;
            }
        }

        body {
            margin: 0;
            overflow-x: hidden;
            height: fit-content;
            background: linear-gradient(45deg, #193E6C 0%, #193E6C 30%, #6699CC 60%, #EBEDF2 70%, #EBEDF2 100%);
            font-family: Arial, Helvetica, sans-serif;
            background-attachment: fixed;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-family: 'Pridi';
            background-size: 175% 175%;
            background-repeat: no-repeat;
            animation: gradientShift 5s infinite;
        }

        /* Style for all select elements */
        select {
            /* Basic styling */
            padding: 1rem 1rem;
            border: 2px solid #6699CC;
            border-radius: 20px;
            /* Rounded corners */
            background-color: #6699CC;
            /* Background color */
            color: black;
            /* Text color */
            font-size: 16px;
            cursor: pointer;
            outline: none;
            appearance: none;
            /* Remove default styling */
            -webkit-appearance: none;
            /* For Safari */
            -moz-appearance: none;
            /* For Firefox */

            /* Add a custom dropdown arrow */
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 12px;
            padding-right: 35px;

            /* Transition for smooth effects */
            transition: all 0.3s ease;
        }

        /* Hover state */
        select:hover {
            background-color: #5a8bb8;
            border-color: #5a8bb8;
        }

        /* Focus state */
        select:focus {
            border-color: #4a7aa3;
            box-shadow: 0 0 0 2px rgba(102, 153, 204, 0.3);
        }

        select option {
            background-color: white;
            color: black;
            padding: 10px;
        }

        /* Style for dropdown menu appearance */
        select {
            transition: opacity 1s ease, transform 1s ease;
        }

        select option:hover {
            background-color: #6699CC;
            color: white;
        }

        /* For browsers that support the ::backdrop pseudo-element */
        select::backdrop {
            background-color: rgba(0, 0, 0, 0.1);
            transition: opacity 0.3s ease;
        }

        .error {
            color: red;
            font-size: 1rem;
            margin-top: 5px;
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
        }

        /* Hide the actual file input */
        .hidden-file-input {
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            position: absolute;
            z-index: -1;
        }

        @media(max-width:1600px) {
            .file-input-label {
                width:10rem;
            }
        }

        /* Style the label to look like your select */
        .file-input-label {
            /* Match your select styles */
            display: inline-block;
            padding: 0 5rem;
            border: 2px solid #6699CC;
            border-radius: 20px;
            background-color: #6699CC;
            color: black;
            font-size: 60%;
            cursor: pointer;
            outline: none;
            transition: all 0.3s ease;

            /* Match your arrow styling */
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 12px;
        }

        /* Hover state */
        .file-input-label:hover {
            background-color: #5a8bb8;
            border-color: #5a8bb8;
        }

        .file-input-label:disabled {
            background: none;
        }

        /* Focus state */
        .hidden-file-input:focus+.file-input-label {
            border-color: #4a7aa3;
            box-shadow: 0 0 0 2px rgba(102, 153, 204, 0.3);
        }
        /* Disabled state for the entire container */
        .custom-file-input input[disabled]~.file-input-label {
            background-color: #f0f0f0;
            /* Light gray background */
            border-color: #cccccc;
            /* Lighter border */
            color: #888888;
            /* Muted text color */
            cursor: not-allowed;
            /* Show "not allowed" cursor */
            background-image: none;
            /* Remove the arrow icon */
        }

        /* Hover state should be neutral when disabled */
        .custom-file-input input[disabled]~.file-input-label:hover {
            background-color: #f0f0f0;
            border-color: #cccccc;
        }

        /* Text display when disabled */
        .custom-file-input input[disabled]~.file-input-label .file-input-text {
            color: #666666;
            font-style: italic;
        }

        /* Show selected filename */
        .file-input-text::after {
            content: attr(data-file);
            margin-left: 10px;
            font-style: italic;
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }
    </style>
</head>


<body style="">
    @if ($nav == 'true')
        @include('Components.NavBar')
    @endif
    @if ($objects)
        <x-banner>{{ Str::upper($object) }}</x-banner>
    @endif
    {{ $slot }}
</body>

<script>
    function setActiveLink() {
        const currentPage = window.location.pathname; // Get the current page path
        const links = document.querySelectorAll('.NavBarText'); // Get all navigation links

        links.forEach(link => {
            if (link.getAttribute('href') === currentPage) {
                link.classList.add('active'); // Add the 'active' class to the current page link
            } else {
                link.classList.remove('active'); // Remove 'active' class from other links
            }
        });
    }
    window.onload = setActiveLink;
</script>
<script>
    document.querySelectorAll('select').forEach(select => {
        select.addEventListener('click', function() {
            this.style.transition = 'all 0.3s ease';
        });
    });
</script>

</html>
