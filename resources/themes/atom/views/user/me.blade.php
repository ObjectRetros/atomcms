<x-app-layout>
    @push('title', auth()->user()->username)

    <div class="col-span-12 space-y-3 md:col-span-9">
        <x-user.me-backdrop :user="$user" />

        <div
            class="flex flex-col gap-3 rounded border bg-white p-1 shadow-sm dark:border-gray-900 dark:bg-gray-800 lg:flex-row lg:items-center lg:justify-between">
            <div
                class="relative flex items-center justify-center rounded bg-[#e9b124] py-2 px-2 text-sm font-semibold dark:text-gray-300 dark:border-gray-700 lg:self-stretch">
                <div class="invisible h-6 w-6 -right-1 rotate-45 bg-[#e9b124] lg:visible lg:absolute"></div>
                <img src="{{ asset('/assets/images/icons/online-friends.png') }}" alt="{{ __('Online Friends') }}"
                     class="mr-2 mb-1 inline-flex" style="max-width: 24px; max-height: 24px">
                <span class="relative text-white">{{ __('Online Friends') }}</span>
            </div>

            <div class="relative flex flex-wrap items-center justify-center gap-2 pl-2 sm:justify-start">
                @foreach ($onlineFriends as $friend)
                    <div data-popover-target="friend-{{ $friend->username }}"
                         style="image-rendering: pixelated; background-image: url({{ setting('avatar_imager') }}{{ $friend->look }}&direction=2&head_direction=3&gesture=sml&action=wav&headonly=1&size=s)"
                         class="inline-block h-10 w-10 rounded-full border-2 border-gray-300 bg-center bg-no-repeat dark:border-gray-900">
                    </div>

                    <div data-popover id="friend-{{ $friend->username }}" role="tooltip"
                        class="invisible absolute z-10 inline-block w-64 rounded-lg border border-gray-200 bg-white text-sm font-light text-gray-500 opacity-0 shadow-xs transition-opacity duration-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400">
                        <div
                            class="rounded-t-lg border-b border-gray-200 bg-gray-100 px-3 py-2 dark:border-gray-600 dark:bg-gray-700">
                            <div
                                class="flex w-full items-center justify-center font-semibold text-gray-900 dark:text-white">
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

        <x-content.content-card icon="friends-icon" classes="border dark:border-gray-900">
            <x-slot:title>
                {{ sprintf(__('User Referrals (%s/%s)'), auth()->user()->referrals->referrals_total ?? 0, setting('referrals_needed')) }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('Referral new users and be rewarded by in-game goods') }}
            </x-slot:under-title>

            <div class="px-2 text-sm dark:text-gray-200">
                {{ __('Here at :hotel we have added a referral system, allowing you to obtain a bonus for every :needed users that registers through your referral link will allow you to claim a reward of :amount diamonds!', ['hotel' => setting('hotel_name'), 'needed' => setting('referrals_needed'), 'amount' => setting('referral_reward_amount')]) }}
                <br>

                <small class="text-gray-400">
                    {{ __('Boosting referrals by making own accounts will lead to removal of all progress, currency, inventory and a potential ban') }}
                </small>

                <div class="grid grid-cols-12 gap-2">
                    <x-form.input classes="col-span-12 md:col-span-10 text-sm" name="referral"
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
                    <button disabled class="mt-2 w-full rounded bg-gray-400 p-2 text-white dark:bg-gray-900">
                        {{ sprintf(__('You need to refer :needed more users, before being able to claim your reward', ['needed' =>auth()->user()->referralsNeeded() ?? 0]),auth()->user()->referrals->referrals_total ?? 0) }}
                    </button>
                @endif
            </div>
        </x-content.content-card>
    </div>

    <div class="col-span-12 space-y-4 md:col-span-3">
        <div class="relative w-full" style="height: 213px">
            <div class="relative swiper articles-slider">
                <div class="swiper-wrapper">
                    @forelse ($articles as $article)
                        <x-article-card :for-slider="true" :article="$article" />
                    @empty
                        <x-filler-article-card />
                    @endforelse
                </div>
            </div>
            <div class="swiper-pagination" style="bottom: 0px !important; z-index: 0;"></div>
        </div>

        <div class="!mt-3">
            <x-user.discord-widget />
        </div>
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
                    title: '{{ __('Your referral code has been copied to your clipbord!') }}'
                });
            }
        </script>
    @endpush
</x-app-layout>
