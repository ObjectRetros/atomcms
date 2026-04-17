<div class="hidden relative w-full h-full flex flex-col items-center gap-y-2 py-3 md:flex md:flex-row md:gap-x-6 md:gap-y-0 md:py-0" id="mobile-menu">

    {{-- HOME / USERNAME --}}
    @if (auth()->check())
    <button
        id="homeDropdown"
        data-dropdown-toggle="home-dropdown"
        class="dark:text-gray-200 {{ request()->is('user*') || request()->is('profile*') ? 'md:border-b-4 md:border-b-[#eeb425]' : '' }} nav-item gap-x-2 ml-5 md:ml-0">
        <img src="{{ setting('avatar_imager') }}{{ auth()->user()->look }}&size=b&head_direction=2&gesture=sml&headonly=1"
             class="w-7 h-7 rounded-full object-cover bg-gray-200 dark:bg-gray-700 overflow-hidden -mt-1"
             style="image-rendering: pixelated;"
             alt="{{ auth()->user()->username }}" />
        {{ auth()->user()->username }}
        <x-icons.chevron-down />
    </button>

    <div id="home-dropdown" class="py-2 hidden z-10 w-44 text-sm bg-white dark:bg-gray-800 shadow-lg rounded-lg block">
        <a href="{{ route('me.show') }}" class="dropdown-item dark:text-gray-200 dark:hover:bg-gray-700">
            🏠 {{ __('Home') }}
        </a>
        <a href="{{ route('profile.show', auth()->user()->username) }}" class="dropdown-item dark:text-gray-200 dark:hover:bg-gray-700">
            👤 {{ __('My Profile') }}
        </a>
        <a href="{{ route('achievements.index') }}" class="dropdown-item dark:text-gray-200 dark:hover:bg-gray-700">
            🏆 {{ __('Başarımlar') }}
        </a>
        <a href="{{ route('settings.account.show') }}" class="dropdown-item dark:text-gray-200 dark:hover:bg-gray-700">
            ⚙️ {{ __('Settings') }}
        </a>
        <hr class="my-1 border-gray-200 dark:border-gray-600">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-item w-full text-left text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20">
                🚪 {{ __('Log out') }}
            </button>
        </form>
    </div>

    @else
    <a href="{{ route('welcome') }}"
       class="nav-item dark:text-gray-200 {{ request()->routeIs('welcome') ? 'md:border-b-4 md:border-b-[#eeb425]' : '' }}">
        <i class="navigation-icon home mr-1 hidden lg:inline-flex"></i>
        {{ __('Home') }}
    </a>
    @endif

    {{-- COMMUNITY --}}
    <button
        id="communityDropdown"
        data-dropdown-toggle="community-dropdown"
        class="dark:text-gray-200 {{ request()->is('community*') ? 'md:border-b-4 md:border-b-[#eeb425]' : '' }} nav-item gap-x-1 ml-5 md:ml-0">
        <i class="navigation-icon community mr-1 hidden lg:inline-flex"></i>
        {{ __('Community') }}
        <x-icons.chevron-down />
    </button>

    <div id="community-dropdown" class="py-2 hidden z-10 w-44 text-sm bg-white dark:bg-gray-800 shadow-lg rounded-lg block">
        <a href="{{ route('article.index') }}" class="dropdown-item dark:text-gray-200 dark:hover:bg-gray-700">
            📰 {{ __('Articles') }}
        </a>
        <a href="{{ route('photos.index') }}" class="dropdown-item dark:text-gray-200 dark:hover:bg-gray-700">
            📷 {{ __('Photos') }}
        </a>
        <a href="{{ route('staff.index') }}" class="dropdown-item dark:text-gray-200 dark:hover:bg-gray-700">
            👑 {{ __('Staff') }}
        </a>
        <a href="{{ route('staff-applications.index') }}" class="dropdown-item dark:text-gray-200 dark:hover:bg-gray-700">
            📋 {{ __('Staff Applications') }}
        </a>
    </div>

    {{-- LEADERBOARDS --}}
    <a href="{{ route('leaderboard.index') }}"
       class="nav-item dark:text-gray-200 {{ request()->routeIs('leaderboard.*') ? 'md:border-b-4 md:border-b-[#eeb425]' : '' }}">
       <i class="navigation-icon leaderboards mr-1 hidden lg:inline-flex"></i>
        {{ __('Leaderboards') }}
    </a>

    {{-- SHOP --}}
    <a href="{{ route('shop.index') }}"
       class="nav-item dark:text-gray-200 {{ request()->routeIs('shop.*') ? 'md:border-b-4 md:border-b-[#eeb425]' : '' }}">
        <i class="navigation-icon mr-1 hidden lg:inline-flex shop"></i>
        {{ __('Shop') }}
    </a>

    {{-- RULES --}}
    <a href="{{ route('rules.index') }}"
       class="nav-item dark:text-gray-200 {{ request()->routeIs('rules.*') ? 'md:border-b-4 md:border-b-[#eeb425]' : '' }}">
        <i class="navigation-icon rules mr-1 hidden lg:inline-flex"></i>
        {{ __('Rules') }}
    </a>

    {{-- DISCORD --}}
    <a href="https://discord.gg/7jftX65hYV" target="_blank" class="nav-item dark:text-gray-200">
        {{ __('Discord') }}
    </a>

</div>
