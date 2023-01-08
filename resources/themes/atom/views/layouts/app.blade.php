<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="turbolinks-cache-control" content="no-cache">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ setting('hotel_name') }} - @stack('title')</title>

        <link rel="icon" type="image/gif" sizes="18x17" href="{{ asset('assets/images/home_icon.gif') }}">

        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <link rel="stylesheet" href="https://unpkg.com/flowbite@1.5.3/dist/flowbite.min.css" />
        <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/animations/scale.css" />
        <link rel="stylesheet" href="https://unpkg.com/flowbite@1.5.3/dist/flowbite.min.css" />

        <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
        <script src="https://unpkg.com/@popperjs/core@2"></script>
        <script src="https://unpkg.com/tippy.js@6"></script>

        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://www.paypal.com/sdk/js?client-id=sb&currency=USD"></script>

        <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/animations/scale.css"/>

        <script defer src="https://unpkg.com/@alpinejs/ui@3.10.5-beta.8/dist/cdn.min.js"></script>
        <script defer src="https://unpkg.com/@alpinejs/focus@3.10.5/dist/cdn.min.js"></script>

        @vite(['resources/themes/atom/css/app.css', 'resources/themes/atom/js/app.js'])
        @stack('scripts')
    </head>

    <body class="flex flex-col min-h-screen site-bg dark:bg-gray-800">
        <x-messages.flash-messages />

        <div id="app" class="bg-gray-100 dark:bg-gray-900">
            {{-- Top header --}}
            @auth
                <x-top-header />
            @endauth

            {{-- Site Header --}}
            <x-site-header />

            {{-- Navigation --}}
            <nav class="relative bg-white shadow dark:bg-gray-900">
                {{-- relative w-full flex flex-col items-center md:flex-row md:items- md:justify-between gap-x-8 uppercase font-semibold text-[14px] mt-5 --}}
                <div class="px-4 mx-auto max-w-7xl h-auto md:h-[60px] flex md:items-center md:justify-between">
                    <div class="h-full w-full">
                        <x-navigation.mobile-menu />

                        <x-navigation.navigation-menu />
                    </div>

                    <x-navigation.theme-mode-switcher />
                    <x-navigation.language-selector>
                        <img src="/assets/images/icons/flags/{{ session()->has('locale') ? session()->get('locale') : config('habbo.site.default_language') }}.png" alt="">
                    </x-navigation.language-selector>
                </div>
            </nav>

            {{-- Content --}}
            <main class="overflow-hidden site-bg">
                <div class="max-w-7xl mx-auto p-6 grid grid grid-cols-12 gap-x-3 gap-y-8 mt-10 md:mt-0">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <x-footer />

        @if(setting('cms_color_mode') === 'dark')
            <script>
                if(localStorage.getItem("theme") === null) {
                    document.documentElement.classList.add("dark");
                    localStorage.setItem("theme", 'dark');
                }
            </script>
        @endif

        @stack('javascript')
    </body>
</html>
