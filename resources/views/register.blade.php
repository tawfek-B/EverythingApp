<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
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
            font-family: Arial, Helvetica, sans-serif;
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
            background: #193E6C;
            cursor: pointer;
            animation: rotateBorder 2s linear infinite;
        }

        .error {
            color: red;
            font-size: 0.875rem;
            margin-top: 5px;
            text-align: center;
        }

        @keyframes rotateBorder {
            0% {
                border-image-source: linear-gradient(45deg, transparent, transparent, transparent, white);
            }

            25% {
                border-image-source: linear-gradient(45deg, white, transparent, transparent, transparent);
            }

            50% {
                border-image-source: linear-gradient(45deg, transparent, white, transparent, transparent);
            }

            75% {
                border-image-source: linear-gradient(45deg, transparent, transparent, white, transparent);
            }

            100% {
                border-image-source: linear-gradient(45deg, transparent, transparent, transparent, white);
            }
        }

        /* Animation Keyframes */
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
                box-shadow:
                    4px 4px 15px #676767
            }

            12.5% {
                box-shadow:
                    4px 0px 15px #676767
            }

            25% {
                box-shadow:
                    4px -4px 15px #676767
            }

            37.5% {
                box-shadow:
                    0 -4px 15px #676767
            }

            50% {
                box-shadow:
                    -4px -4px 15px #676767
            }

            62.5% {
                box-shadow:
                    -4px 0 15px #676767
            }

            75% {
                box-shadow:
                    -4px 4px 15px #676767
            }

            87.5% {
                box-shadow:
                    0 4px 15px #676767
            }

            100% {
                box-shadow:
                    4px 4px 15px #676767
            }

        }



        @media (max-width: 768px) {
            .form {
                width: 95%;

                margin: 10% auto;
            }

            .name {
                font-size: 2rem;
            }

            .button {
                font-size: 1rem;
                padding: 10px;
            }
        }

        @media (max-width: 480px) {
            .name {
                font-size: 1.75rem;
            }

            .textfield {
                font-size: 0.875rem;
            }
        }
    </style>
</head>

<body>
    <div class="form">
        <div class="logo">
            <a href="/" class="logo">
                <img src="/Web/EVERYTHING1.png" alt="Everything App Logo" class="logo">
            </a>
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
                <button class="button">Hover Me!</button>
            </div>
        </form>
    </div>
</body>

</html>
