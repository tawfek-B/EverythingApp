@props(['objects' => false, 'object', 'nav' => 'true'])
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Just+Another+Hand&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap"
        rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Everything App</title>

    <style>
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
            font-family: 'Just Another Hand';
            background-size: 175% 175%;
            background-repeat: no-repeat;
            animation: gradientShift 5s infinite;
        }

        .error {
            color: red;
            font-size: 1rem;
            margin-top: 5px;
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
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

</html>
