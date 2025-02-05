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
            height: 160vh;
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
        .UserContainer {
            width: 80%;
            height: 100%;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-gap: 20px;
        }
        .User {
            background: #193E6C;
            padding: 5px 0;
            margin-top: 2%;
            font-size: 30px;
            border: #6699CC 4px solid;
            color: white;
            border-radius: 3px;
            display: flex;
            flex-direction: row;
            transition: 0.3s ease;
            align-items: center;
            justify-content: center;
            max-height:175px;
        }

        .User:hover {
            background-color: #6699CC;
            border: #6699CC 4px solid;
            border-radius: 10px;
            color: white;
        }
        .disable-hover .User:hover {
            background: #193E6C;
            padding: 5px 0;
            margin-top: 2%;
            font-size: 20px;
            border: #6699CC 4px solid;
            color: white;
            border-radius: 3px;
            display: flex;
            flex-direction: row;
            transition: 0.3s ease;
            align-items: center;
        }
        .textContainer {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            line-height: 30px;
            z-index: 2;
        }
        .line {
            width:47%;
            height:15px;
            background:linear-gradient(90deg, white, #6699CC, #193E6C);
            border:black 1px solid;
        }
        #circle {
            position: fixed;
            /* Use fixed to ensure it follows the mouse */
            width: 450px;
            height: 450px;
            background-color: #193E6C;
            border-radius: 50%;
            pointer-events: none;
            /* Ensures the circle doesn't interfere with mouse events */
            transform: translate(-50%, -50%);
            opacity: 0;
            z-index: 1;
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

<body>
    @include('Components.NavBar')
    <div style="display: flex; flex-direction:row;width:100%;justify-content:center; align-items:center; margin-bottom:2.5%;">
        <div class="line"style="margin-left:auto;margin-right:auto;border-right:none"></div>
        <div style="text-align: center;border:#193E6C 4px solid; border-radius:10px;background-color:#6699CC; width:auto; min-width:15%;">
            <div style=" font-size:32.5px;">
                USERS
            </div>
        </div>
        <div class="line"style="margin-left:auto;margin-right:auto;background:linear-gradient(90deg, #193E6C, #6699CC, white);border-left:none"></div>
    </div>
    <div class="UserContainer">
        @foreach (App\Models\User::paginate(10) as $user)
            <a href="/user/{{ $user->id }}" class="User" style="text-decoration: none;"
                id="button{{ $user->id }}">
                <div class="textContainer">
                    User name: {{ $user->userName }}<br>
                    Number: {{$user->number}}
                </div>
            </a>
            <div id="circle" class="circle"></div>
        @endforeach
    </div>
    <div style="margin-top:2.5%; width:50px; height:50px;">
        {{ App\Models\User::paginate(10)->links() }}
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const buttons = document.querySelectorAll('.User'); // Select all buttons
            const circle = document.getElementById('circle'); // Select the circle

            buttons.forEach(button => {
                button.addEventListener('mousemove', (event) => {
                    const buttonRect = button.getBoundingClientRect();
                    const mouseX = event.clientX;
                    const mouseY = event.clientY;

                    // Check if the mouse is near or on the button
                    const isNearButton =
                        mouseX >= buttonRect.left &&
                        mouseX <= buttonRect.right &&
                        mouseY >= buttonRect.top &&
                        mouseY <= buttonRect.bottom;

                    if (isNearButton) {
                        // Position the circle at the mouse cursor
                        circle.style.left = `${mouseX}px`;
                        circle.style.top = `${mouseY}px`;
                        circle.style.opacity = '1';
                        circle.style.zIndex = '1';

                        // Calculate the clip-path based on the button's boundaries
                        const clipTop = Math.max(buttonRect.top - mouseY + 230, 0);
                        const clipRight = Math.max(mouseX - buttonRect.right + 230, 0);
                        const clipBottom = Math.max(mouseY - buttonRect.bottom + 230, 0);
                        const clipLeft = Math.max(buttonRect.left - mouseX + 230, 0);
                        circle.style.clipPath =
                            `inset(${clipTop}px ${clipRight}px ${clipBottom}px ${clipLeft}px)`;
                    }
                });

                button.addEventListener('mouseleave', () => {
                    circle.style.opacity = '0';
                });
                document.addEventListener('scroll', () => {
                    circle.style.opacity = '0';
                });
                let scrollTimer;
                document.addEventListener('scroll', () => {
                    document.body.classList.add('disable-hover');
                    clearTimeout(scrollTimer);
                    scrollTimer = setTimeout(() => {
                        document.body.classList.remove('disable-hover');
                    }, 200);
                })
            });
        });
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

        // Call the function when the page loads
        window.onload = setActiveLink;
    </script>
</body>

</html>
