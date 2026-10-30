<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Sign Up Notification</title>
</head>

<body>
    <div class="container">
        <img src="{{ asset('landing/assets/img/gallery/sioshipping_logo.png') }}" alt="sioshipping Logo" height="100px">
        <p>Dear {{$user->name}}</p>
        <p>
            You are recieving this mail because your regiatration on Sioshipping.eu with email {{$user->email}} was successful<br>
            Welcome to Siopay please proceed to login at <a href="https://www.sioshipping.eu/">Sioshipping</a> to Enjoy our services today. <br>
            <br>
            <br>
            Your Registration Number is <b>{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</b> 
            <br>@if ($user->user_type == "dispatcher")
                Email : <b>{{ $user->email }}</b>
                <br>
                Password : <b>{{ $password }}</b> <br>
            @else
                
            @endif, and you will recieve a mail shortly from our KYC team
            concerning the status of your registration, mail support@sioshipping.eu if you have any questions.
            <br>
            Welcome
        </p>
        <hr>
        &copy; {{date('Y')}} <a href="https://www.sioshipping.eu/">{{env('APP_NAME')}}</a>
    </div>
</body>

</html>

