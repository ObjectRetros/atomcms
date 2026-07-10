<nav class="nav-header" x-data="{
    open: false,
 }">
    <div class="w-full min-h-[60px] text-white px-5 relative">
        <button @click="open = !open" class="absolute right-5 top-5">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>

        <div x-show="open" class="flex flex-col text-white gap-x-14 p-4 space-y-3">
            <x-navigation.dropdown route-group="help-center*" :show-chevron="true" :flex-col="false">
                {{ __('Community') }}

                <x-slot:children>
                    <x-navigation.dropdown-child :route="route('article.index')">
                        {{ __('Articles') }}
                    </x-navigation.dropdown-child>


                    <x-navigation.dropdown-child :route="route('staff.index')">
                        {{ __('Staff') }}
                    </x-navigation.dropdown-child>

                    <x-navigation.dropdown-child :route="route('teams.index')">
                        {{ __('Teams') }}
                    </x-navigation.dropdown-child>

                    <x-navigation.dropdown-child :route="route('staff-applications.index')">
                        {{ __('Staff applications') }}
                    </x-navigation.dropdown-child>

                    <x-navigation.dropdown-child :route="route('help-center.index')">
                        {{ __('Help center') }}
                    </x-navigation.dropdown-child>

                    <x-navigation.dropdown-child :route="route('photos.index')">
                        {{ __('Photos') }}
                    </x-navigation.dropdown-child>
                </x-slot:children>
            </x-navigation.dropdown>

            <a href="" class="transition ease-in-out hover:text-[#ac93da]">
                {{ __('Leaderboard') }}
            </a>

            <a href="" class="transition ease-in-out hover:text-[#ac93da]">
                {{ __('News') }}
            </a>

            <a href="" class="transition ease-in-out hover:text-[#ac93da]">
                {{ __('Events') }}
            </a>

            <a href="" class="transition ease-in-out hover:text-[#ac93da]">
                {{ __('Store') }}
            </a>

            <x-navigation.dropdown route-group="help-center*"  :show-chevron="true" :flex-col="false">
                {{ __('Home') }}

                <x-slot:children>
                    @auth
                        <x-navigation.dropdown-child :route="route('profile.show', Auth::user()->username)">
                            {{ __('My profile') }}
                        </x-navigation.dropdown-child>


                        <x-navigation.dropdown-child :route="route('settings.account.show')">
                            {{ __('Account settings') }}
                        </x-navigation.dropdown-child>

                        <button class="dropdown-item dark:text-gray-200 dark:hover:bg-gray-700 w-full text-left" @click.stop.prevent="document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </button>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @endauth

                    @guest
                        <x-navigation.dropdown-child :route="route('login')">
                            {{ __('Login') }}
                        </x-navigation.dropdown-child>


                        <x-navigation.dropdown-child :route="route('register')">
                            {{ __('Register') }}
                        </x-navigation.dropdown-child>
                    @endguest
                </x-slot:children>
            </x-navigation.dropdown>
        </div>
    </div>
</nav>
