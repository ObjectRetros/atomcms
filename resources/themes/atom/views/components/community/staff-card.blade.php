@props(['user'])

<div class="relative h-24 w-full overflow-hidden rounded border bg-white dark:border-gray-900 dark:bg-gray-700 md:mt-0">
    <div class="absolute top-1 right-1 rounded bg-white px-2 text-sm font-semibold dark:bg-gray-900 dark:text-gray-300">
        {{ $user->permission?->rank_name }}
    </div>

    <div class="h-[65%] w-full staff-bg"
        style="background: rgba(0, 0, 0, 0.5) url({{ asset(sprintf('assets/images/%s', $user->permission?->staff_background ?? 'staff-bg.png')) }});">
    </div>

    <div class="absolute top-4 left-1 drop-shadow">
        <a href="{{ route('profile.show', $user->username) }}">
            <img style="image-rendering: pixelated;" class="transition duration-300 ease-in-out hover:scale-105"
                src="{{ setting('avatar_imager') }}{{ $user->look }}&direction=2&head_direction=3&gesture=sml&action=wav"
                alt="">
        </a>
    </div>

    <p class="text-2xl font-semibold ml-[70px] text-white -mt-[35px]">
        {{ $user->username }}
    </p>

    <div class="flex w-full items-center justify-between px-4">
        <p class="ml-[57px] text-sm mt-[10px] font-semibold text-gray-500 truncate">
            {{ Str::limit($user->motto, 20) }}
        </p>

        <div
            class="min-w-[15px] max-w-[15px] min-h-[15px] max-h-[15px] rounded-full mt-2 flex items-start {{ $user->online ? 'bg-green-600' : 'bg-red-600' }}">
        </div>
    </div>
</div>
