<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="turbolinks-cache-control" content="no-cache">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ setting('hotel_name') }} - @stack('title')</title>

    <link rel="icon" type="image/gif" sizes="18x17" href="{{ asset('assets/images/home_icon.gif') }}">

    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fonts.css') }}">

    @vite(['resources/css/global.css', 'resources/js/global.js'], 'build')
    @stack('scripts')
</head>

<body class="flex justify-center items-center min-h-screen site-bg">
<main class="mx-auto w-full lg:w-[40%] p-4 lg:px-0 lg:py-4">
    {{ $slot }}
</main>
</body>
</html>
