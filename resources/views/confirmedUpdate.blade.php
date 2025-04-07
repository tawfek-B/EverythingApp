<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Confirmation</title>
    <style>
        .logo {
            width: 25%;
            height: 12.5%;
            margin-right: auto;
            margin-left: auto;
        }

        .logoContainer {
            height: auto;
            width: auto;
            margin-top: 5%;
            display: flex;
            flex-direction: row;
        }
    </style>
    <script>
        const link = @json(session('link', '/welcome'));
        console.log(link);
        // Redirect after 3 seconds
        setTimeout(() => {
            window.location.href = link;
        }, 3000);
    </script>
</head>

<x-layout :nav=false>

    <div class="logoContainer">
        <img src="Images/Web/EVERYTHING1.png" alt="" class="logo">
    </div>

    <div style="text-align: center; margin-top: 2%; font-size:2.5rem">
        @if (session('update_info'))
            @php
                $info = session('update_info');
            @endphp
            @if ($info['name'] == 'delete subs')
                <h1>
                    Subscriptions deleted successfully
                </h1>
            @else
                <h1>
                    {{ $info['name'] }} updated successfully!
                </h1>
            @endif
        @else
            <h1>Nothing to confirm!</h1>
        @endif
        <p>You will be redirected shortly...</p>
    </div>
</x-layout>

</html>
