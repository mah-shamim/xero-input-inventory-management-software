<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/reportApp.js') }}" defer></script>


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <!-- Styles -->
    <link href="{{ asset('css/reportApp.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        [v-cloak] {display: none}
    </style>
    @if(session()->has('settings'))
        <meta name="settings" content="{{session()->get('settings')}}">
    @endif
    <script>
        window.auth = {!! json_encode([
            'csrfToken' => csrf_token(),
            'user' => Auth::user(),
            'api_token' => (Auth::user()) ? Auth::user()->api_token : null
        ]) !!};
        window.settings = {!!json_encode(session()->get('settings'))!!};
    </script>
</head>

<body>
<div id="app" v-cloak>
    <v-app id="inspire">
        @include('pages.layout.report.topbar')
        @include('pages.layout.report.sidebar')
        <v-main>
            <v-container
                fluid
                fill-height
            >
                <v-layout>
                    <router-view></router-view>
                </v-layout>
            </v-container>
        </v-main>
        <v-footer app>
            <span>&copy; 2020; <a target="_blank">XiroInput</a></span>
        </v-footer>
    </v-app>
</div>
</body>
</html>
