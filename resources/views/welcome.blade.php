<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons">
    <link rel="stylesheet" href="{{mix('css/static.css')}}">
    <title>Xero Input</title>
</head>
<body class="landing">
<div class="tw-w-2/3 tw-mx-auto">
    @include('homepagePartials.navbar')
</div>

@include('homepagePartials.banner')
<div class="tw-container main-container tw-rounded-lg tw-shadow-xl tw-pt-64 tw-mx-auto tw-w-2/3">
    @include('homepagePartials.container')
</div>
<script src="{{mix('js/static.js')}}"></script>
</body>
</html>
