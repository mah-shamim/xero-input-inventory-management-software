<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('documentations/js/documentation.js') }}" defer></script>


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <!-- Styles -->
    <link href="{{ asset('documentations/css/documentation.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        [v-cloak] {
            display: none
        }
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
        <v-navigation-drawer app>
            <!-- -->
        </v-navigation-drawer>

        <v-app-bar app>
            <!-- -->
        </v-app-bar>

        <!-- Sizes your content based upon application components -->
        <v-main>

            <!-- Provides the application the proper gutter -->
            <v-container fluid>

                <!-- If using vue-router -->
                <router-view></router-view>
            </v-container>
        </v-main>

        <v-footer app>
            <!-- -->
        </v-footer>
    </v-app>
</div>
</body>