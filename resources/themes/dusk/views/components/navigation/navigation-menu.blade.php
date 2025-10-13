<nav class="nav-header">
    <div class="max-w-7xl w-full flex justify-between items-center h-[120px]">
        <a href="/" class="transition duration-300 ease-in-out hover:scale-105">
            <img src="{{ setting('cms_logo') }}" alt="">
        </a>

        <div class="flex text-white gap-x-14">
            <x-navigation.dropdown icon="community_icon.png" route-group="help-center*" :uppercase="true">
                {{ __('Community') }}

                <x-slot:children>
                    <x-navigation.dropdown-child :route="route('staff.index')">
                        {{ __('Staff') }}
                    </x-navigation.dropdown-child>

                    <x-navigation.dropdown-child :route="route('teams.index')">
                        {{ __('Teams applications') }}
                    </x-navigation.dropdown-child>

                    <x-navigation.dropdown-child :route="route('staff-applications.index')">
                        {{ __('Staff applications') }}
                    </x-navigation.dropdown-child>

                    <x-navigation.dropdown-child :route="route('values.index')">
                        {{ __('Rare values') }}
                    </x-navigation.dropdown-child>

                    <x-navigation.dropdown-child :route="route('help-center.index')">
                        {{ __('Help center') }}
                    </x-navigation.dropdown-child>

                    <x-navigation.dropdown-child :route="route('photos.index')">
                        {{ __('Photos') }}
                    </x-navigation.dropdown-child>
                </x-slot:children>
            </x-navigation.dropdown>

            <a href="{{ route('leaderboard.index') }}" class="flex flex-col gap-1 items-center transition ease-in-out hover:text-[#ac93da]">
                <img class="icon" src="{{ asset('/assets/images/dusk/leaderboard_icon.png') }}" alt="community icon">
                Leaderboards
            </a>

            <a href="{{ route('article.index') }}" class="flex flex-col gap-1 items-center transition ease-in-out hover:text-[#ac93da]">
                <img class="icon" src="{{ asset('/assets/images/dusk/news_icon.png') }}" alt="community icon">
                News
            </a>

            {{--
            <a href="#" class="flex flex-col gap-1 items-center transition ease-in-out hover:text-[#ac93da]">
                <img class="icon" src="{{ asset('/assets/images/dusk/events_icon.png') }}" alt="community icon">
                Events
            </a>
            --}}


            <a href="{{ route('shop.index') }}" class="flex flex-col gap-1 items-center transition ease-in-out hover:text-[#ac93da]">
                <img class="icon" src="{{ asset('/assets/images/dusk/store_icon.png') }}" alt="community icon">
                Store
            </a>

            <x-navigation.dropdown icon="home_icon.png" route-group="user*" :uppercase="true">
                {{ __('Home') }}

                <x-slot:children>
                    @auth
                        <x-navigation.dropdown-child :route="route('profile.show', Auth::user()->username)">
                            {{ __('My profile') }}
                        </x-navigation.dropdown-child>
						
						<x-navigation.dropdown-child :route="route('draw-badge')">
							{{ __('Badge Drawer') }}
						</x-navigation.dropdown-child>

                        <x-navigation.dropdown-child :route="route('settings.account.show')">
                            {{ __('Account settings') }}
                        </x-navigation.dropdown-child>

                        <button class="dropdown-item" @click.stop.prevent="document.getElementById('logout-form').submit();">
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
