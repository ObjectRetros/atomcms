<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fonts.css') }}">

    @vite(['resources/themes/' .  setting('theme') . '/css/app.css', 'resources/themes/' .  setting('theme') . '/js/app.js'], 'build')
</head>

<body>
    <div id="app" class="font-sans text-gray-900 antialiased">
        {{ $slot }}
    </div>
</body>

</html>
