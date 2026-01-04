
@props(['user'])

<div class="relative flex items-center justify-between overflow-hidden rounded px-10 me-backdrop"
    style="background: rgba(0, 0, 0, 0.3) url({{ setting('cms_me_backdrop') }});">
    <div>
        <a href="{{ route('profile.show', $user) }}"
            class="absolute -bottom-12 left-0 drop-shadow transition duration-300 ease-in-out hover:scale-105">
            <img style="image-rendering: pixelated;"
                src="{{ setting('avatar_imager') }}{{ $user->look }}&direction=2&head_direction=3&gesture=sml&action=wav&size=l"
                alt="">
        </a>
    </div>

    <a data-turbolinks="false" href="{{ route('nitro-client') }}">
        <button
            class="relative rounded-full bg-white/90 px-6 py-2 text-lg font-semibold text-black transition duration-300 ease-in-out hover:bg-white dark:bg-gray-900 dark:text-white">
            {{ __('Go to :hotel', ['hotel' => setting('hotel_name')]) }}
        </button>
    </a>
</div>
