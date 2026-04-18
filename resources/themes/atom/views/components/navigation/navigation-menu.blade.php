<div class="hidden relative w-full h-full flex-col items-center gap-y-2 py-3 md:flex md:flex-row md:items-center md:justify-between md:gap-y-0 md:py-0 md:h-full" id="mobile-menu">

    {{-- ── MAIN NAV LINKS ── --}}
    <div class="flex flex-col gap-y-1 md:flex-row md:items-center md:h-full md:gap-x-0">

        {{-- Username / Home --}}
        @if (auth()->check())
        <a href="{{ route('me.show') }}"
           class="nav-item dark:text-gray-200 gap-x-1.5 ml-5 md:ml-0 px-3
                  {{ request()->is('user/me') ? 'md:border-b-[#eeb425]' : '' }}">
            <img src="{{ setting('avatar_imager') }}{{ auth()->user()->look }}&size=b&head_direction=2&gesture=sml&headonly=1"
                 class="w-6 h-6 rounded-full object-cover bg-gray-200 dark:bg-gray-700 overflow-hidden"
                 style="image-rendering: pixelated;"
                 alt="{{ auth()->user()->username }}" />
            {{ auth()->user()->username }}
        </a>
        @else
        <a href="{{ route('welcome') }}"
           class="nav-item dark:text-gray-200 ml-5 md:ml-0 px-3
                  {{ request()->routeIs('welcome') ? 'md:border-b-[#eeb425]' : '' }}">
            {{ __('Home') }}
        </a>
        @endif

        {{-- Articles --}}
        <a href="{{ route('article.index') }}"
           class="nav-item dark:text-gray-200 ml-5 md:ml-0 px-3
                  {{ request()->routeIs('article.*') ? 'md:border-b-[#eeb425]' : '' }}">
            {{ __('Articles') }}
        </a>

        {{-- Staff --}}
        <a href="{{ route('staff.index') }}"
           class="nav-item dark:text-gray-200 ml-5 md:ml-0 px-3
                  {{ request()->routeIs('staff.*') || request()->is('community/staff') ? 'md:border-b-[#eeb425]' : '' }}">
            {{ __('Staff') }}
        </a>

        {{-- Leaderboards --}}
        <a href="{{ route('leaderboard.index') }}"
           class="nav-item dark:text-gray-200 ml-5 md:ml-0 px-3
                  {{ request()->routeIs('leaderboard.*') ? 'md:border-b-[#eeb425]' : '' }}">
            {{ __('Leaderboards') }}
        </a>

        {{-- Shop --}}
        <a href="{{ route('shop.index') }}"
           class="nav-item dark:text-gray-200 ml-5 md:ml-0 px-3
                  {{ request()->routeIs('shop.*') ? 'md:border-b-[#eeb425]' : '' }}">
            {{ __('Shop') }}
        </a>

        {{-- Rules --}}
        <a href="{{ route('rules.index') }}"
           class="nav-item dark:text-gray-200 ml-5 md:ml-0 px-3
                  {{ request()->routeIs('rules.*') ? 'md:border-b-[#eeb425]' : '' }}">
            {{ __('Rules') }}
        </a>

        {{-- Discord --}}
        <a href="https://discord.gg/7jftX65hYV" target="_blank"
           class="nav-item dark:text-gray-200 ml-5 md:ml-0 px-3">
            {{ __('Discord') }}
        </a>

        {{-- More (compressed: Photos, Achievements, Profile, Settings) --}}
        @if (auth()->check())
        <button
            id="moreDropdown"
            data-dropdown-toggle="more-dropdown"
            class="nav-item dark:text-gray-200 ml-5 md:ml-0 px-3 gap-x-1">
            {{ __('More') }}
            <x-icons.chevron-down />
        </button>
        <div id="more-dropdown"
             class="py-2 hidden z-10 w-44 text-sm bg-white dark:bg-gray-800 shadow-lg rounded-lg block">
            <a href="{{ route('photos.index') }}" class="dropdown-item dark:text-gray-200 dark:hover:bg-gray-700">
                📷 {{ __('Photos') }}
            </a>
            <a href="{{ route('achievements.index') }}" class="dropdown-item dark:text-gray-200 dark:hover:bg-gray-700">
                🏆 {{ __('Başarımlar') }}
            </a>
            <a href="{{ route('profile.show', auth()->user()->username) }}" class="dropdown-item dark:text-gray-200 dark:hover:bg-gray-700">
                👤 {{ __('My Profile') }}
            </a>
            <a href="{{ route('settings.account.show') }}" class="dropdown-item dark:text-gray-200 dark:hover:bg-gray-700">
                ⚙️ {{ __('Settings') }}
            </a>
        </div>
        @endif
    </div>

    {{-- ── LOGOUT (right-aligned) ── --}}
    @if (auth()->check())
    <form method="POST" action="{{ route('logout') }}" class="ml-5 md:ml-0 flex-shrink-0">
        @csrf
        <button type="submit"
                class="nav-item text-red-500 dark:text-red-400 hover:text-red-600 dark:hover:text-red-300 px-3
                       md:border-transparent md:hover:border-b-red-400">
            {{ __('Çıkış Yap') }}
        </button>
    </form>
    @endif

</div>
