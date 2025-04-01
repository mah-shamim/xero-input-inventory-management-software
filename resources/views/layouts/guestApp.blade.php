<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

{{--    <script src="{{ asset('js/sweetalert.min.js') }}" defer></script>--}}
{{--    <script src="{{ asset('js/print.min.js') }}" defer></script>--}}
{{--    <script src="{{ asset('js/chart.min.js') }}" defer></script>--}}

    <!-- Styles -->
    <link href="{{ mix('css/static.css') }}" rel="stylesheet">
    <!-- Scripts -->

    {{--    <link href="{{ asset('css/sweetalert.css') }}" rel="stylesheet">--}}
    {{--    <link href="{{ asset('css/print.min.css') }}" rel="stylesheet">--}}
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        [v-cloak] {
            display: none
        }
    </style>
    @yield('style-login')
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-10941758750">
    </script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'AW-10941758750');
    </script>
</head>
<body>
<div id="app">
    @yield('content')
</div>
<script src="{{ mix('js/static.js') }}"></script>
</body>
</html>
