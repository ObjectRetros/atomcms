<div class="w-full h-52 relative flex items-center justify-center header-bg" style="background: url({{ setting('cms_header') }});">
    <div class="w-full h-full bg-black absolute bg-opacity-50"></div>

    @auth
        <div class="max-w-7xl relative h-full w-full flex items-center justify-center md:justify-between pr-10">
            <div class="flex gap-x-4 items-center">
                <a href="{{ route('me.show') }}" class="ml-7">
                    <img class="drop-shadow transition ease-in-out duration-300 hover:scale-105" src={{ setting('cms_logo') }} alt="Hotel logo">
                </a>

                <div class="hidden md:flex items-center bg-white dark:bg-gray-900 dark:text-white px-4 rounded-md relative h-[50px]">
                    <div class="absolute bg-white w-6 h-6 -left-1 rotate-45 dark:bg-gray-900"></div>

                    <span class="relative">
                        {{ __(':online :hotel online', ['online' => DB::table('users')->where('online', '1')->count(), 'hotel' => setting('hotel_name')]) }}
                    </span>
                </div>
            </div>



            <div class="flex gap-x-4">
                <a data-turbolinks="false" href="{{ route('nitro-client') }}">
                    <button class="hidden md:block text-lg relative dark:bg-gray-900 dark:text-white rounded-full py-2 px-6 bg-white bg-opacity-90 transition duration-300 ease-in-out hover:bg-opacity-100 text-black font-semibold">
                        {{ __('Nitro client') }}
                    </button>
                </a>
            </div>
        </div>
    @endauth

    @guest
       <x-modals.modal-wrapper>
           <div class="flex justify-center">
               <div class="text-white font-semibold flex-col md:w-[600px]">
                   <p class="text-center text-xl hidden md:block">
                       {{ __('A online virtual world where you can create your own avatar, make friends, chat, create rooms and much more!') }}
                   </p>

                   <div class="flex flex-col md:flex-row justify-center items-center gap-y-4 md:gap-y-0 gap-x-6 md:mt-6">
                       <button type="button" x-on:click="open = true" class="uppercase border-2 border-white px-8 py-2 rounded-full transition ease-in-out duration-200 hover:bg-white hover:text-black">
                           {{ __('Login') }}
                       </button>

                       <p class="text-opacity-80 text-sm uppercase">{{ __('Or') }}</p>

                       <a href="{{ route('register') }}">
                           <button class="uppercase bg-green-600 bg-opacity-80 px-8 py-2.5 rounded-full transition ease-in-out duration-200 hover:bg-opacity-100">
                               {{ __('Create account') }}
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
