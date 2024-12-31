<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        @vite(['resources/themes/' .  setting('theme') . '/css/app.scss', 'resources/themes/' .  setting('theme') . '/js/app.js'], 'build')
        @turnstileScripts()

        <script src="{{ asset('/assets/js/dusk.js') }}"></script>
    </head>
    <body class="font-sans antialiased select-none">
        <div id="app" class="min-h-screen bg-[#262a35] relative">
            <x-messages.flash-messages />

            {{-- Desktop navigation --}}
            <div class="hidden lg:block">
                <x-navigation.navigation-menu />
            </div>


            {{-- Mobile navigation --}}
            <div class="block lg:hidden">
                <x-navigation.mobile-navigation-menu />
            </div>

            {{-- Sub header --}}
            <div class="sub-header px-5 xl:px-0">
                <div class="max-w-7xl w-full h-[40px] flex items-center justify-between">
                    <div class="flex gap-4 items-center z-20 relative">
                        <div>
                            <x-navigation.language-selector>
                                <img src="/assets/images/icons/flags/{{ session()->has('locale') ? session()->get('locale') : config('habbo.site.default_language') }}.png"
                                     alt="">
                            </x-navigation.language-selector>
                        </div>

                        <a href="{{ setting('discord_invitation_link') }}" target="_blank" class="transition duration-300 ease-in-out hover:text-gray-300">
                            Discord
                        </a>
                    </div>

                   <div class="flex gap-4 items-center">

                       <a href="{{ route('help-center.rules.index') }}" class="transition duration-300 ease-in-out hover:scale-110">
                           <img src="{{ asset('/assets/images/dusk/rules_icon.png') }}" alt="">
                       </a>

                       @if(hasPermission('generate_logo'))
                           <a href="{{ route('logo-generator.index') }}" target="_blank" class="transition duration-300 ease-in-out hover:scale-110">
                               <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                   <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                               </svg>

                           </a>
                       @endif

                       @if(hasPermission('view_server_logs'))
                           <a href="/log-viewer" target="_blank" class="transition duration-300 ease-in-out hover:scale-110">
                               <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                   <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                               </svg>

                           </a>
                       @endif

                       @if(hasPermission('housekeeping_access'))
                           <a href="{{ setting('housekeeping_url') }}" target="_blank" class="transition duration-300 ease-in-out hover:scale-110">
                               <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                   <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                                   <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                               </svg>
                           </a>
                       @endif
                   </div>
                </div>
            </div>

            {{-- Site background --}}
            <div class="site-bg w-full"></div>

            <!-- Page Content -->
            <main class="main-content">
                <div class="max-w-7xl w-full grid grid-cols-12 gap-4 relative py-4 lg:py-20 px-4 xl:px-0">
                    {{ $slot }}
                </div>

            </main>
        </div>

        <x-footer />

        <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />

        @stack('javascript')

        @stack('scripts')
    </body>
</html>
