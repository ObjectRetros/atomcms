<button
    data-collapse-toggle="mobile-menu"
    type="button"
    class="absolute right-16 top-3 md:hidden hover:text-gray-900 dark:text-white dark:hover:text-white z-10" aria-controls="mobile-menu"
    aria-expanded="false">
    <span class="sr-only">{{ __('Open main menu') }}</span>
    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd"
              d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
              clip-rule="evenodd"></path>
    </svg>
</button>

@auth
<form method="POST" action="{{ route('logout') }}" class="absolute right-6 top-3 md:hidden z-10">
    @csrf
    <button type="submit" class="text-red-500 dark:text-red-400 hover:text-red-700" title="{{ __('Log out') }}">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
    </button>
</form>
@endauth
