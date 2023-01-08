<div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8 items-center flex justify-between py-2 dark:bg-gray-900">
    <div class="flex gap-x-6">
        <x-top-header-currency icon="nav-credit-icon">
            <x-slot:currency>
                {{ auth()->user()->credits }}
            </x-slot:currency>

            {{ __('Credits') }}
        </x-top-header-currency>

        <x-top-header-currency icon="nav-ducket-icon">
            <x-slot:currency>
                {{ auth()->user()->currency('duckets') }}
            </x-slot:currency>

            {{ __('Duckets') }}
        </x-top-header-currency>

        <x-top-header-currency icon="nav-diamond-icon">
            <x-slot:currency>
                {{ auth()->user()->currency('diamonds') }}
            </x-slot:currency>

            {{ __('Diamonds') }}
        </x-top-header-currency>
    </div>

    <div class="flex w-full justify-between md:w-auto">
        @auth
            @if(hasPermission('view_server_logs') || hasPermission('housekeeping_access'))
            <button
                id="administrationDropdown"
                data-dropdown-toggle="administration-dropdown"
                class="h-10 text-sm font-semibold text-red-700 flex items-center gap-x-1 ml-5 md:ml-0">
                {{ __('Administration') }}

                <x-icons.chevron-down />
            </button>

            <div id="administration-dropdown" class="py-2 hidden z-10 w-44 text-sm bg-white dark:bg-gray-800 shadow block">
                @if(hasPermission('view_server_logs'))
                    <a data-turbolinks="false" href="/log-viewer" target="_blank" class="dropdown-item dark:text-gray-200 dark:hover:bg-gray-700">
                        {{ __('Error logs') }}
                    </a>
                @endif

                @if(hasPermission('housekeeping_access'))
                    <a data-turbolinks="false" href="{{ setting('housekeeping_url') }}" target="_blank" class="dropdown-item dark:text-gray-200 dark:hover:bg-gray-700">
                        {{ __('Housekeeping') }}
                    </a>
                @endif
            </div>
            @endif
        @endauth

        <x-navigation.user-dropdown />
    </div>
</div>
