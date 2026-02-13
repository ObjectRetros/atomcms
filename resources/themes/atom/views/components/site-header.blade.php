<div class="relative flex h-52 w-full items-center justify-center header-bg"
    style="background: url({{ setting('cms_header') }});">
    <div class="absolute h-full w-full bg-black/50"></div>

    @auth
        <div class="relative flex h-full w-full max-w-7xl items-center justify-center pr-10 md:justify-between">
            <div class="flex items-center gap-x-4">
                <a href="{{ route('me.show') }}" class="ml-7">
                    <img class="drop-shadow transition duration-300 ease-in-out hover:scale-105"
                        src="{{ setting('cms_logo') }}" alt="Hotel logo">
                </a>

                <div
                    class="hidden md:flex items-center bg-white dark:bg-gray-900 dark:text-white px-4 rounded-md relative h-[50px]">
                    <div class="absolute -left-1 h-6 w-6 rotate-45 bg-white dark:bg-gray-900"></div>

                    <span class="relative">
                        {{ __(':online :hotel online', ['online' => DB::table('users')->where('online', '1')->count(),'hotel' => setting('hotel_name')]) }}
                    </span>
                </div>
            </div>

            <flex class="flex gap-x-4">
                <a data-turbolinks="false" href="{{ route('nitro-client') }}">
                    <button
                        class="relative hidden rounded-full bg-white/90 px-6 py-2 text-lg font-semibold text-black transition duration-300 ease-in-out hover:bg-white dark:bg-gray-900 dark:text-white md:block">
                        {{ __('Nitro client') }}
                    </button>
                </a>

            </flex>
        </div>
    @endauth

    @guest
        <x-modals.modal-wrapper>
            <div class="flex justify-center">
                <div class="text-white font-semibold flex-col md:w-[600px]">
                    <p class="hidden text-center text-xl md:block">
                        {{ __('An online virtual world where you can create your own avatar, make friends, chat, create rooms and much more!') }}
                    </p>

                    <div class="flex flex-col items-center justify-center gap-x-6 gap-y-4 md:mt-6 md:flex-row md:gap-y-0">
                        <button type="button" x-on:click="open = true"
                            class="rounded-full border-2 border-white px-8! py-2! uppercase transition! duration-200! ease-in-out! hover:bg-white hover:text-black!">
                            {{ __('Login') }}
                        </button>

                        <p class="text-sm uppercase text-white/80">{{ __('Or') }}</p>

                        <a data-turbolinks="false" href="{{ route('register') }}">
                            <button
                                class="uppercase bg-green-600/80! px-8! py-2.5! rounded-full transition! ease-in-out! duration-200! hover:bg-green-600! text-white">
                                {{ __('Create an account') }}
                            </button>
                        </a>
                    </div>
                </div>
            </div>

            <x-modals.regular-modal x-model="show {{ session()->get('wrong-auth') }}">
                <x-auth.login-form />
            </x-modals.regular-modal>
        </x-modals.modal-wrapper>

    @endguest
</div>
