<x-app-layout>
    @push('title', __('Welcome to the best hotel on the web!'))


    <div class="col-span-12 md:col-span-8 h-[250px] bg-gray-900/50 rounded-xl flex lg:px-8 py-8 items-center text-white relative overflow-hidden">
        <div class="block lg:hidden w-40">
            <a href="{{ route('profile.show', $user) }}"
               class="absolute bottom-2 -left-4 lg:left-8 drop-shadow transition duration-300 ease-in-out hover:scale-105">
                <img style="image-rendering: pixelated;"
                     src="{{ setting('avatar_imager') }}{{ $user->look }}&direction=2&head_direction=3&gesture=sml&action=wav&size=l"
                     alt="">
            </a>
        </div>

        <div class="z-10">
            <div class="hidden lg:block h-[200px] w-[200px] rounded-full relative overflow-hidden" style="background: url('/assets/images/dusk/me_circle_image.png')">
                <div>
                    <a href="{{ route('profile.show', $user) }}"
                       class="absolute -bottom-12 left-2 lg:left-8 drop-shadow transition duration-300 ease-in-out hover:scale-105">
                        <img style="image-rendering: pixelated;"
                             src="{{ setting('avatar_imager') }}{{ $user->look }}&direction=2&head_direction=3&gesture=sml&action=wav&size=l"
                             alt="">
                    </a>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row justify-between self-start w-full px-4">
            <div class="flex flex-col gap-1 self-start lg:ml-2 py-2 text-white ">
                <h2 class="text-3xl font-semibold">
                    Hey {{ Auth::user()->username }}!
                </h2>

                <p class="italic">{{ Auth::user()->motto }}</p>
            </div>

           <div class="self-start lg:ml-14 w-full lg:w-64">
               <a href="{{ route('nitro-client') }}">
                   <button type="submit" class="w-full text-white bg-yellow-500 border-2 border-yellow-300 w-full rounded transition duration-300 ease-in-out hover:scale-[102%] py-2 px-4">
                       {{ __('Go to :hotel', ['hotel' => setting('hotel_name')]) }}
                   </button>
               </a>
           </div>
        </div>

        <div class="absolute w-full bottom-0 left-0 h-20 bg-gray-900 py-4 lg:pl-64 px-4 lg:px-0">
            <div class="flex gap-x-6 h-full items-center justify-center lg:justify-start">
                <x-currency icon="nav-credit-icon">
                    <x-slot:currency>
                        {{ auth()->user()->credits }}
                    </x-slot:currency>

                    <span class="hidden lg:block">{{ __('Credits') }}</span>
                </x-currency>

                <x-currency icon="nav-ducket-icon">
                    <x-slot:currency>
                        {{ auth()->user()->currency('duckets') }}
                    </x-slot:currency>

                    <span class="hidden lg:block"> {{ __('Duckets') }}</span>
                </x-currency>

                <x-currency icon="nav-diamond-icon">
                    <x-slot:currency>
                        {{ auth()->user()->currency('diamonds') }}
                    </x-slot:currency>

                    <span class="hidden lg:block">{{ __('Diamonds') }}</span>
                </x-currency>
            </div>
        </div>
    </div>


    {{-- Articles --}}
    <div class="col-span-12 md:col-span-4 h-[250px]">
        <!-- Slider main container -->
        <div class="swiper h-[250px] rounded-md">

            <!-- If we need pagination -->
            <div class="swiper-pagination"></div>

            <!-- If we need navigation buttons -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>

            <!-- Additional required wrapper -->
            <div class="swiper-wrapper" style="z-index: 14;">
                @foreach($articles as $article)
                    <x-article-card :article="$article" />
                @endforeach
            </div>
        </div>
    </div>

   <div class="col-span-12 md:col-span-8">
       <div
           class="flex flex-col gap-3 rounded bg-[#2b303c] p-1 shadow-md lg:flex-row lg:items-center lg:justify-between">
           <div
               class="relative flex items-center justify-center rounded bg-[#e9b124] py-2 px-2 text-sm font-semibold">
               <div class="invisible h-6 w-6 -right-1 rotate-45 bg-[#e9b124] lg:visible lg:absolute"></div>
               <img src="{{ asset('/assets/images/icons/online-friends.png') }}" alt="{{ __('Online Friends') }}"
                    class="mr-2 mb-1 inline-flex" style="max-width: 24px; max-height: 24px">
               <span class="relative text-white">{{ __('Online Friends') }}</span>
           </div>

           <div class="relative flex flex-wrap items-center justify-center gap-2 pl-2 sm:justify-start">
               @foreach ($onlineFriends as $friend)
                   <div data-popover-target="friend-{{ $friend->username }}"
                        style="image-rendering: pixelated; background-image: url({{ setting('avatar_imager') }}{{ $friend->look }}&direction=2&head_direction=3&gesture=sml&action=wav&headonly=1&size=s)"
                        class="inline-block h-10 w-10 rounded-full border-2 border-gray-300 bg-center bg-no-repeat">
                   </div>

                   <div data-popover id="friend-{{ $friend->username }}" role="tooltip"
                        class="invisible absolute z-10 inline-block w-64 rounded-lg border border-gray-200 bg-white text-sm font-light text-gray-500 opacity-0 shadow-xs transition-opacity duration-300">
                       <div
                           class="rounded-t-lg border-b border-gray-200 bg-gray-100 px-3 py-2">
                           <div
                               class="flex w-full items-center justify-center font-semibold text-gray-900">
                               {{ $friend->username }}
                           </div>
                       </div>
                       <div class="overflow-y-auto px-3 py-2" style="max-height: 200px">
                           <b class="mr-1 font-bold">{{ __('Motto') }}:</b>{{ $friend->motto }}<br>
                           <b
                               class="mr-1 font-bold">{{ __('Online Since') }}
                               :</b>{{ date(config('habbo.site.date_format'), $friend->last_online) }}
                       </div>
                       <div data-popper-arrow></div>
                   </div>
               @endforeach
           </div>
       </div>
   </div>

    <div class="col-span-12 md:col-span-8">
        <x-content.content-card icon="hotel-icon">
            <x-slot:title>
                {{ sprintf(__('User Referrals (%s/%s)'), auth()->user()->referrals->referrals_total ?? 0, setting('referrals_needed')) }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('Referral new users and be rewarded by in-game goods') }}
            </x-slot:under-title>

            <div class="text-sm">
                {{ __('Here at :hotel we have added a referral system, allowing you to obtain a bonus for every :needed users that registers through your referral link will allow you to claim a reward of :amount diamonds!', ['hotel' => setting('hotel_name'), 'needed' => setting('referrals_needed'), 'amount' => setting('referral_reward_amount')]) }}
                <br>

                <small class="text-gray-300">
                    {{ __('Boosting referrals by making own accounts will lead to removal of all progress, currency, inventory and a potential ban') }}
                </small>

                <div class="grid grid-cols-12 gap-2 mt-2">
                    <x-form.input classes="col-span-12 md:col-span-10" name="referral"
                                  value="{{ sprintf('%s/register/%s', config('habbo.site.site_url'), auth()->user()->referral_code) }}"
                                  :autofocus="false" :readonly="true" />

                    <div class="col-span-12 flex md:col-span-2" onclick="copyCode()">
                        <x-form.secondary-button>
                            {{ __('Copy code') }}
                        </x-form.secondary-button>
                    </div>

                </div>

                @if (auth()->user()->referrals?->referrals_total >= (int) setting('referrals_needed'))
                    <a href="{{ route('claim.referral-reward') }}" class="text-decoration-none">
                        <x-form.secondary-button classes="mt-2">
                            {{ __('Claim your referrals reward!') }}
                        </x-form.secondary-button>
                    </a>
                @else
                    <button disabled class="mt-2 w-full rounded bg-[#171a23] p-2 text-white">
                        {{ sprintf(__('You need to refer :needed more users, before being able to claim your reward', ['needed' =>auth()->user()->referralsNeeded() ?? 0]),auth()->user()->referrals->referrals_total ?? 0) }}
                    </button>
                @endif
            </div>
        </x-content.content-card>
    </div>

    @push('javascript')
        <script>
            var Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener("mouseenter", Swal.stopTimer);
                    toast.addEventListener("mouseleave", Swal.resumeTimer);
                }
            });

            function copyCode() {
                let copyText = document.querySelector("#referral");
                copyText.select();
                document.execCommand("copy");

                Toast.fire({
                    icon: "success",
                    title: '{{ __('Your referral code has been copied to your clipbord!') }}',
                    customClass: {
                        container: 'dark-mode-toast' // Add this class for dark mode
                    },
                });
            }
        </script>
    @endpush
</x-app-layout>
