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
            height: 90vh;
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

        .AdminContainer {
            width: 30%;
            height: 150%;
            display: flex;
            flex-direction: column;
            border:black 5px solid;
            /* justify-content: center; */
            align-items: center;
            background:#6699CC;
            justify-content: center;
            border-radius:15px;
            margin-bottom:3%;
        }

        .Admin {
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
        }

        .Admin:hover {
            background-color: #6699CC;
            border: #193E6C 4px solid;
            border-radius: 10px;
            color: black;
        }

        .textContainer {
            line-height: 50px;
            z-index: 2;
            font-size: 30px;
        }

        .buttonContainer {
            display: flex;
            flex-direction: column;
            align-items:center;
            column-gap: 5px;
            row-gap: 10px;
            height: 25%;
            width: 15%;
        }

        .button {
            border: 1px white solid;
            text-decoration: none;
            font-size: 20px;
            color: black;
            text-align: center;
            height:25%;
            width:50%;
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
    <div class="AdminContainer">
        <img src="{{ asset('/Web/EVERYTHING1.png') }}" alt="Admin Image"
            style="width:125px; height:100px; z-index:2; margin-bottom:5%;"><!-- \server-->
        <div class="textContainer">
            Admin name: {{ App\Models\Admin::where('id', session('admin'))->first()->name }}<br>
            Admin user name: {{App\Models\Admin::where('id', session('admin'))->first()->userName}}<br>
            Privileges:
            @if(App\Models\Admin::where('id', session('admin'))->first()->privileges==0)
            Teacher
            @elseif (App\Models\Admin::where('id', session('admin'))->first()->privileges == 1)
            Semi-Admin
            @else
            Admin
            @endif<br>
            @if (App\Models\Admin::where('id', session('admin'))->first()->teacher_id!=null)
            Teacher: {{App\Models\Teacher::where('id', App\Models\Admin::where('id', session('admin'))->first()->teacher_id)->first()->name}}
            @endif

        </div>
    </div>
    <div class="buttonContainer">
        <div style="">
            <a href="/subject/{{ session('subject') }}/lectures" class="button" style="background-color:#6699CC;">Edit Lectures</a>
            <a href="/subject/{{ session('subject') }}/edit" class="button" style="background-color:#193E6C;">Edit Subject</a>
        </div>
        <a href="/subject/{{ session('subject') }}/delete" class="button" style="background-color:red;">Delete
            Subject</a>
    </div>

</body>

</html>
