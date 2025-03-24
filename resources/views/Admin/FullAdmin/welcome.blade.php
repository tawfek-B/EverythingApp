<head>
    <style>
        .logo {
            width: 300px;
            height: 250px;
            margin-left: auto;
            margin-right: auto;
        }

        .logoContainer {
            height: auto;
            width: auto;
            dislpay: flex;
            flex-direction: row;
        }

        .button {
            width: 90%;
            margin-left: auto;
            margin-right: auto;
            margin-top: 10%;
            background: #6699CC;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: black 2.5px solid;
            transition: 0.3s ease;
        }

        .button:hover {
            border-radius: 10px;
            border: #6699CC 2.5px solid;
        }

        .disable-hover .button:hover {
            width: 30%;
            margin-left: auto;
            margin-right: auto;
            margin-top: 3.5%;
            background: #6699CC;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: black 2.5px solid;
        }

        .text {
            position: inherit;
            color: black;
            text-decoration: none;
            text-align: center;
            font-size: 20px;
            /* text-shadow: white 2px 2px 5px; */
            z-index: 2;
        }

        .buttonContainer {
            width: 100%;
            height: 50%;
            display: flex;
            flex-direction: column;
        }

        .title {
            margin-left: auto;
            margin-right: auto;
            font-size: 30px;
        }

        #circle {
            position: fixed;
            /* Use fixed to ensure it follows the mouse */
            width: 250px;
            height: 250px;
            background-color: #193E6C;
            border-radius: 50%;
            pointer-events: none;
            /* Ensures the circle doesn't interfere with mouse events */
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }
    </style>
</head>
<x-layout : nav=false>

    <div class="logo">
        <div class="logo">
            <img src="/Web/EVERYTHING1.png" alt="" class="logo">
        </div>
    </div>
    <div class="title">
        Welcome {{ Auth::user()->userName }}!
    </div>
    <div class="buttonContainer">
        <div style="display:grid; grid-template-columns: 1fr 1fr; width:50%; margin-right:auto; margin-left:auto; margin-top:auto;margin-bottom:auto; gap:5px">
            <a href="/universities" class="button" id="button" style="text-decoration: none;">
                <div class="text">Universites</div>
            </a>
            <a href="/subjects" class="button" id="button" style="text-decoration: none;">
                <div class="text">Subjects</div>
            </a>
            <a href="/lectures" class="button" style="text-decoration: none;">
                <div class="text">Lectures</div>
            </a>
            <a href="/teachers" class="button" style="text-decoration: none;">
                <div class="text">Teachers</div>
            </a>
            <a href="/users" class="button" style="text-decoration: none;">
                <div class="text">Users</div>
            </a>
            <a href="/admins" class="button" id="button" style="text-decoration: none;">
                <div class="text">Admins</div>
            </a>
        </div>

    </div>
    <div class="circle"id="circle"></div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const buttons = document.querySelectorAll('.button'); // Select all buttons
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
                        const clipTop = Math.max(buttonRect.top - mouseY + 130, 0);
                        const clipRight = Math.max(mouseX - buttonRect.right + 130, 0);
                        const clipBottom = Math.max(mouseY - buttonRect.bottom + 130, 0);
                        const clipLeft = Math.max(buttonRect.left - mouseX + 130, 0);
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
    </script>
</x-layout>
