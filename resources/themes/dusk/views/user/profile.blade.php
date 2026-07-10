<x-app-layout>
    @push('title', $user->username)
    @push('meta_description', __(":username's profile on :hotel. Check out their badges, rooms and friends!", ['username' => $user->username, 'hotel' => setting('hotel_name')]))

    <div class="col-span-12 md:col-span-8 md:max-h-[250px] bg-gray-900/50 rounded-xl text-gray-200">
        <div class="flex mt-6">
            <div class="ml-6 hidden md:block">
                <div class="md:h-[200px] md:w-[200px] rounded-full relative overflow-hidden"
                     style="background: url('/assets/images/dusk/me_circle_image.png')">
                    <div>
                        <a href="{{ route('profile.show', $user) }}"
                           class="absolute -bottom-12 left-8 lg:left-8 drop-shadow transition duration-300 ease-in-out hover:scale-105">
                            <img style="image-rendering: pixelated;"
                                 src="{{ setting('avatar_imager') }}{{ $user->look }}&direction=2&head_direction=3&gesture=sml&action=wav&size=l"
                                 alt="">
                        </a>
                    </div>
                </div>
            </div>

            <div class="flex flex-col w-full">
                <div class="flex flex-col md:flex-row justify-between w-full px-4 pr-6">
                    <div class="flex flex-col gap-1 self-start lg:ml-2 py-2 text-white">
                        <h4 class="text-lg font-semibold">
                            {{ __('My name is,') }}
                        </h4>

                        <h2 class="text-3xl font-semibold">
                            {{ $user->username }}
                        </h2>

                        <div class="flex flex-col">
                            <p class="mt-4 italic">{{ $user->motto }}</p>
                            <small class="text-gray-400">{{ __('Last online: :date', ['date' => date('Y-m-d', $user->last_online)]) }}</small>
                        </div>
                    </div>

                    <div class="flex flex-col align-content-end gap-y-4 h-full mt-4">
                        <x-currency icon="nav-credit-icon">
                            <x-slot:currency>
                                {{ auth()->user()->credits }}
                            </x-slot:currency>

                            <span>{{ __('Credits') }}</span>
                        </x-currency>

                        <x-currency icon="nav-ducket-icon">
                            <x-slot:currency>
                                {{ auth()->user()->currency('duckets') }}
                            </x-slot:currency>

                            <span> {{ __('Duckets') }}</span>
                        </x-currency>

                        <x-currency icon="nav-diamond-icon">
                            <x-slot:currency>
                                {{ auth()->user()->currency('diamonds') }}
                            </x-slot:currency>

                            <span>{{ __('Diamonds') }}</span>
                        </x-currency>
                    </div>
                </div>

                <div class="flex justify-end md:pr-6">

                    <div class="flex items-center gap-3 mt-6 text-xs ml-6 md:ml-8">
                        @foreach($user->badges as $badge)
                            <div>
                                <img src="{{ setting('badges_path') }}/{{  $badge->badge_code }}.gif" alt="">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-10">
            @foreach($photos as $photo)
                <a href="{{ $photo->url }}" data-fancybox="gallery"
                   class="cursor-pointer relative transition duration-300 ease-in-out hover:scale-[102%]">
                    <div class="photo-overlay"></div>
                    <img class="h-[250px] w-full object-cover object-center rounded-md custom-shadow"
                         src="{{ $photo->url }}" alt="">

                    <div class="absolute right-2 bottom-2 bg-black/70 p-2 rounded-md text-white flex gap-x-2 z-[5]">
                        <img class="self-center" src="{{ asset('/assets/images/dusk/author_camera_icon.png') }}" alt="">
                        <small>
                            {{ $photo->user->username }}
                        </small>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <div class="col-span-12 md:col-span-4">
        <x-content.content-card>
            <x-slot:title>
                {{  __('Rooms') }}
            </x-slot:title>

            <div class="grid grid-cols-3 sm:grid-cols-6 md:grid-cols-3 lg:grid-cols-4 gap-4 w-full">
                @forelse($user->rooms as $room)
                    <div class="flex flex-col justify-center">
                        <div class="relative w-[80px] h-[80px]">
                            <img
                                class="rounded"
                                src="{{ asset('/assets/images/profile/room_placeholder.png') }}"
                                alt="Test"
                            >

                            <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2">
                                <div
                                    class="{{ 0 > 0 ? 'bg-[#00800B]' : 'bg-gray-400' }} px-1 py-[1px] font-semibold rounded flex gap-x-[3px] text-white items-center text-xs">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-[12px]" viewBox="0 0 20 20"
                                         fill="currentColor">
                                        <path
                                            fill-rule="evenodd"
                                            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>

                                    0
                                </div>
                            </div>
                        </div>

                        <p class="mt-4 ml-2 text-sm truncate  w-[80px]">
                            {{ $room->name }}
                        </p>
                    </div>
                @empty
                    <div class="col-span-12 text-center">
                        <p>{{ __(':username does not have any rooms yet.', ['username' => $user->username]) }}</p>
                    </div>
                @endforelse
            </div>
        </x-content.content-card>

        <div class="mt-4">
            <x-content.content-card>
                <x-slot:title>
                    {{  __('Guestbook') }}
                </x-slot:title>

                <div class="flex flex-col gap-2">
                    @foreach($guestbook as $post)
                        <div class="bg-gray-600 p-2 rounded-md text-gray-200 h-[60px] overflow-hidden">
                            <div class="flex relative">
                                <img class="-mt-5 drop-shadow"
                                     src="{{ setting('avatar_imager') }}{{ $post->user?->look }}"
                                     alt="{{ $post->user?->username }}">

                                <div class="flex flex-col">
                                    <div class="w-full flex items-center">
                                        <a href="{{ route('profile.show', $post->user ?? $user) }}"
                                           class="text-blue-300 hover:underline">
                                            {{ $post->user?->username }}
                                        </a>

                                        @if($post->profile_id === Auth::id() || $user->id === Auth::id() || Auth::user()->rank > (int)setting('min_staff_rank'))
                                            <form action="{{ route('guestbook.destroy', [$user, $post]) }}" method="POST">
                                                @method('DELETE')
                                                @csrf

                                                <button type="submit" class="text-white">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    <span>{{ Str::limit($post->message, 30) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <form class="flex flex-col md:flex-row gap-2 mt-2" action="{{ route('guestbook.store', $user) }}"
                          method="POST">
                        @csrf

                        <x-form.input classes="w-full md:w-2/3" name="message"
                                      placeholder="{{ __('Write something on their guestbook') }}"
                                      :autofocus="false"/>

                        <div class="w-full md:w-1/3">
                            <x-form.secondary-button>
                                {{ __('Post message') }}
                            </x-form.secondary-button>
                        </div>
                </div>
        </div>
        </x-content.content-card>
    </div>
    </div>


    @push('javascript')
        <script type="module">
            tippy('.user-badge');
            tippy('.friend');
            tippy('.group');
        </script>
    @endpush
</x-app-layout>
