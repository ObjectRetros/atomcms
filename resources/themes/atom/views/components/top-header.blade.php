<div class="max-w-7xl min-h-[60px] px-4 md:flex md:items-center md:justify-between md:mx-auto">
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

    <div class="flex gap-x-3">
        @if(hasPermission('view_server_logs') || hasPermission('housekeeping_access') || hasPermission('generate_logo'))
            <x-navigation.dropdown classes="!text-red-700 !border-none">
                {{ __('Administration') }}

                <x-slot:children>
                    @if (hasPermission('generate_logo'))
                        <x-navigation.dropdown-child route="{{ route('logo-generator.index') }}" :turbolink="false" target="_blank">
                            {{ __('Logo generator') }}
                        </x-navigation.dropdown-child>
                    @endif

                    @if(hasPermission('housekeeping_access'))
                        <a data-turbolinks="false" href="{{ setting('housekeeping_url') }}" target="_blank" class="dropdown-item dark:text-gray-200 dark:hover:bg-gray-700">
                            {{ __('Housekeeping') }}
                        </a>
                    @endif
                </x-slot:children>
            </x-navigation.dropdown>
        @endif

        <x-navigation.dropdown classes="!border-none">
            <img class="h-12" src="{{ setting('avatar_imager') }}{{ auth()->user()->look }}&direction=2&headonly=1&head_direction=2&gesture=sml" alt="{{ auth()->user()->username }}">

            <span class="-ml-2">{{ auth()->user()->username }}</span>

            <x-slot:children>
                <x-navigation.dropdown-child :route="route('settings.account.show')">
                    {{ __('User settings') }}
                </x-navigation.dropdown-child>

                <button class="dropdown-item dark:text-gray-200 dark:hover:bg-gray-700 w-full text-left" @click.prevent="document.getElementById('logout-form').submit();">
                    {{ __('Logout') }}
                </button>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </x-slot:children>
        </x-navigation.dropdown>
    </div>
</div>
