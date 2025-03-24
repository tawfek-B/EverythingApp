<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Just+Another+Hand&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Everything App</title>
    <style>
    .just-another-hand-regular {
        font-family: "Just Another Hand", serif;
        font-weight: 400;
        font-style: normal;
      }
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0;
            font-family:'Just Another Hand', cursive;
        }
        .title {
            height:50%;
            margin-left:auto;
            margin-right:auto;
            justify-items: flex-start;
            font-size:40px;
            text-align:center;
            line-height:120%;
        }
        .subjectContainer{
            width:50%;
            display:flex;
            flex-direction: column;
        }
        .subject {
            margin-right:auto;
            margin-left:auto;
            width:100px;
            background:#6699CC;
            color:white;
            border-radius: 5px;
            border:1.5px #000000 solid;
            margin-bottom:20%;
            text-align: center;
            font-size:25px;
            text-decoration: none;
        }
        #hoverButton {
            padding: 20px 40px;
            font-size: 16px;
            background-color: white;
            border: 2px solid black;
            cursor: pointer;
            position: relative;
            z-index: 1;
        }
        #text {
            color:purple;
            z-index:3;
            font-size:15px;
            cursor:pointer;
            position: absolute;
            top:50%;
            top:50%;
            left:50%;
            transform: translate(-50%, -50%);
        }
        #circle {
            position: fixed;
            /* Use fixed to ensure it follows the mouse */
            width: 50px;
            height: 50px;
            background-color: black;
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

<body>
    <div class="title">WELCOME TEACHER<br> {{Auth::user()->name}}</div>
    <div class="subjectContainer">
        @foreach(App\Models\Subject::all() as $subject)
        @if($subject->teacher_id==Auth::id())
        <a href="/subject/{{$subject->id}}" class="subject" style="font-family:'Just Another Hand'">{{$subject->name}}</a>
        @endif
        @endforeach</div>
    <button id="hoverButton">
        <div id="text">
            Hover Me
        </div>
    </button>
    <div id="circle"></div>

    <script>
        const button = document.getElementById('hoverButton');
        const circle = document.getElementById('circle');

        document.addEventListener('mousemove', (event) => {
            const buttonRect = button.getBoundingClientRect();
            const mouseX = event.clientX;
            const mouseY = event.clientY;

            // Check if the mouse is near or on the button
            const isNearButton = (
                mouseX >= buttonRect.left &&
                mouseX <= buttonRect.right &&
                mouseY >= buttonRect.top &&
                mouseY <= buttonRect.bottom
            );

            if (isNearButton) {
                // Position the circle at the mouse cursor
                circle.style.left = `${mouseX}px`;
                circle.style.top = `${mouseY}px`;
                circle.style.opacity = '1';
                circle.style.zIndex='1';

                // Calculate the clip-path based on the button's boundaries
                const clipTop = Math.max(buttonRect.top - mouseY + 26, 0);
                const clipRight = Math.max(mouseX - buttonRect.right + 25, 0);
                const clipBottom = Math.max(mouseY - buttonRect.bottom + 26, 0);
                const clipLeft = Math.max(buttonRect.left - mouseX + 25, 0);
                circle.style.clipPath = `inset(${clipTop}px ${clipRight}px ${clipBottom}px ${clipLeft}px)`;
            }
            else {
                circle.style.opacity=0;
            }
        });
    </script>
</body>

</html>
