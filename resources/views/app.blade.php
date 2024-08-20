<!DOCTYPE html>
<html lang="en" class="h-full">

<head>

    @if(isset($key))
    <title>{{ $key }}</title>
    @else
    <title>Corebill a Care One Communications Solution</title>
    @endif
    <script src="/assets/js/pace/pace.js"></script>
    <link href="{{ mix('/assets/css/custom.css') }}" rel="stylesheet" type="text/css">

    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600&display=swap" rel="stylesheet">

    @if(isset($img))
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $img }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ $img }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $img }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $img }}">
    <link rel="manifest" href="{{ $img }}">
    <link rel="mask-icon" href="{{ $img }}" color="#5851d8">
    <link rel="shortcut icon" href="{{ $img }}">
@else
<link rel="apple-touch-icon" sizes="180x180" href="/assets/img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/img/favicons/favicon-16x16.png">
    <link rel="manifest" href="/assets/img/favicons/site.webmanifest">
    <link rel="mask-icon" href="/assets/img/favicons/safari-pinned-tab.svg" color="#5851d8">
    <link rel="shortcut icon" href="/assets/img/favicons/favicon.ico">
@endif





    <!-- <link rel="apple-touch-icon" sizes="180x180" href="/assets/img/favicons/apple-touch-icon.png"> -->
    <!-- <link rel="icon" type="image/png" sizes="32x32" href="/assets/img/favicons/favicon-32x32.png"> -->
    <!-- <link rel="icon" type="image/png" sizes="16x16" href="/assets/img/favicons/favicon-16x16.png"> -->
    <!-- <link rel="manifest" href="/assets/img/favicons/site.webmanifest"> -->
    <!-- <link rel="mask-icon" href="/assets/img/favicons/safari-pinned-tab.svg" color="#5851d8"> -->
    <!-- <link rel="shortcut icon" href="/assets/img/favicons/favicon.ico"> -->
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-config" content="/assets/img/favicons/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script type="text/javascript" src="https://js.braintreegateway.com/web/dropin/1.13.0/js/dropin.min.js"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

</head>

<body class="h-full overflow-x-hidden bg-gray-100 layout-default skin-crater font-base">
    <div id="app" class="h-full">
        <router-view></router-view>
    </div>
    <script type="text/javascript" src="{{mix('/assets/js/app.js')}}"></script>
</body>

</html>
