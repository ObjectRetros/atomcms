<button id="dropdownNavbarLink" data-dropdown-toggle="dropdownNavbarUser"
    class="ml-4 flex items-center dark:text-gray-200">
    <div class="h-10">
        <img class="w-10"
            src="{{ setting('avatar_imager') }}{{ auth()->user()->look }}&direction=2&headonly=1&head_direction=2&gesture=sml"
            alt="">
    </div>

    <span>{{ auth()->user()->username }}</span>

    <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
        stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
    </svg>
</button>

<!-- Dropdown menu -->
<div id="dropdownNavbarUser" class="z-10 hidden w-44 bg-white py-2 shadow-sm dark:bg-gray-800">
    <a href="{{ route('settings.account.show') }}"
        class="block px-4 py-2 font-semibold hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-200">
        {{ __('User settings') }}
    </a>

    @auth
        <a href="{{ route('logout') }}"
            class="block px-4! py-2! font-semibold hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-200"
            onclick="event.preventDefault();
            document.getElementById('logout-form').submit();">
            {{ __('Logout') }}
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    @endauth
</div>
