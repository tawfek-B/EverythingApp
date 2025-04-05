<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pridi:wght@200;300;400;500;600;700&display=swap"
        rel="stylesheet">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Everything App</title>
    <style>
        body {
            margin: 0;
            height: 90vh;
            background: linear-gradient(45deg, #193E6C 0%, #193E6C 30%, #6699CC 60%, #EBEDF2 70%, #EBEDF2 100%);
            background-size: 175% 175%;
            background-repeat: no-repeat;
            animation: gradientShift 5s infinite;
            font-family: 'Pridi', Arial, Helvetica, sans-serif;
            background-attachment: fixed;
        }

        .form {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: auto;
            min-height: 80vh;
            width: 90%;
            max-width: 400px;
            margin: 5% auto;
            border: 3px black solid;
            border-radius: 9px;
            padding: 30px;
            box-sizing: border-box;

            background-color: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .logo {
            width: 80%;
            margin: 0 auto;
            display: flex;
            justify-content: center;
        }

        .logo img {
            max-width: 100%;
            height: auto;
            margin-bottom: 10%;
        }

        .name {
            text-align: center;
            font-size: 2.5rem;
            color: black;
            margin-top: -5%;
            margin-bottom: 10%;
        }

        .textfieldContainer {
            width: 100%;
            margin: 10px 0;
        }

        .textfield {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1rem;
            box-sizing: border-box;
            margin-top: 15px;
            transition: 0.1s ease;
        }

        .textfield:focus {
            outline: none;
            background-color: ghostwhite;
            border: 3px solid #193E6C;
        }

        .button {
            position: relative;
            width: 100%;
            padding: 15px;
            font-size: 1.25rem;
            background: #6699CC;
            color: white;
            border: 3px solid transparent;
            cursor: pointer;
            margin-top: 60px;
            transition: 0.5s ease;
            z-index: 1;
        }

        .button {
            border-image: linear-gradient(45deg, transparent, transparent, transparent, transparent);
            border-image-slice: 1;
            border-image-width: 2px;
            border-image-outset: 1px;
        }

        .button:hover {
            background-color: rgba(25, 62, 109, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            cursor: pointer;
            animation: rotateBorder 2s linear infinite;
            animation-duration: forwards;
        }

        .error {
            color: red;
            font-size: 1rem;
            margin-top: 5px;
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
        }

        @keyframes rotateBorder {
            0% {
                border-image-source: linear-gradient(90deg, transparent, transparent, transparent, white);
            }

            25% {
                border-image-source: linear-gradient(90deg, black, transparent, transparent, transparent);
            }

            50% {
                border-image-source: linear-gradient(90deg, transparent, white, transparent, transparent);
            }

            75% {
                border-image-source: linear-gradient(90deg, transparent, transparent, black, transparent);
            }

            100% {
                border-image-source: linear-gradient(90deg, transparent, transparent, transparent, white);
            }
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

        @keyframes rotateShadow {
            0% {
                box-shadow: 4px 4px 15px #676767;
            }

            12.5% {
                box-shadow: 4px 0px 15px #676767;
            }

            25% {
                box-shadow: 4px -4px 15px #676767;
            }

            37.5% {
                box-shadow: 0 -4px 15px #676767;
            }

            50% {
                box-shadow: -4px -4px 15px #676767;
            }

            62.5% {
                box-shadow: -4px 0 15px #676767;
            }

            75% {
                box-shadow: -4px 4px 15px #676767;
            }

            87.5% {
                box-shadow: 0 4px 15px #676767;
            }

            100% {
                box-shadow: 4px 4px 15px #676767;
            }
        }

        /* Media Queries for Responsive Design */
        @media (max-width: 768px) {
            .form {
                width: 95%;
                margin: 10% auto;
                padding: 20px;
                /* Reduce padding for smaller screens */
            }

            .name {
                font-size: 2rem;
                /* Reduce font size for smaller screens */
            }

            .button {
                font-size: 1rem;
                /* Reduce button font size for smaller screens */
                padding: 10px;
                /* Reduce button padding for smaller screens */
            }

            .textfield {
                font-size: 0.875rem;
                /* Reduce text field font size for smaller screens */
            }
        }

        @media (max-width: 480px) {
            .form {
                width: 100%;
                /* Make the form full width on very small screens */
                margin: 5% auto;
                padding: 15px;
                /* Further reduce padding for very small screens */
            }

            .name {
                font-size: 1.75rem;
                /* Further reduce font size for very small screens */
            }

            .button {
                font-size: 0.875rem;
                /* Further reduce button font size for very small screens */
                padding: 8px;
                /* Further reduce button padding for very small screens */
            }

            .textfield {
                font-size: 0.75rem;
                /* Further reduce text field font size for very small screens */
            }
        }
    </style>
</head>

<body>
    <div class="form">
        <div class="logo">
            <img src="/Web/EVERYTHING1.png" alt="Everything App Logo" class="logo">
        </div>
        <div class="name">Everything App</div>
        <form class="container" method="POST" action="/weblogin">
            @csrf
            <div class="textfieldContainer">
                <input class="textfield" id="userName" type="text" name="userName" placeholder="Username"
                    style="text-align:center;" autocomplete="off" title="" value="{{ old('userName') }}" required>
            </div>
            @error('userName')
                <div class="error">{{ $message }}</div>
            @enderror
            <div class="textfieldContainer">
                <input class="textfield" type="password" name="password" placeholder="Password"
                    style="text-align:center;" autocomplete="off" title="" value="{{ old('password') }}" required>
            </div>
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror
            <div>
                {{-- <input class="button" type="submit" value="Log in"> --}}
                <button class="button">Log in</button>
            </div>
        </form>
    </div>
</body>

</html>
