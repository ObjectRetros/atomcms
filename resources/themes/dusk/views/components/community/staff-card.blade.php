@props(['user'])

<div class="relative h-24 w-full overflow-hidden rounded-lg bg-[#171a23] md:mt-0">
    <div class="h-[65%] w-full staff-bg"
        style="background: rgba(0, 0, 0, 0.6) url({{ asset(sprintf('assets/images/%s', $user->permission?->staff_background ?? 'staff-bg.png')) }});">
    </div>

    <div class="absolute left-3 top-3">
        <div class="w-16 h-16 rounded-full relative overflow-hidden" style="background-size: contain; background-image: url('/assets/images/dusk/me_circle_image.png')">
            <div>
                <a href="{{ route('profile.show', $user) }}"
                   class="absolute -bottom-10 drop-shadow transition duration-300 ease-in-out hover:scale-105">
                    <img style="image-rendering: pixelated;"
                         src="{{ setting('avatar_imager') }}{{ $user->look }}&direction=2&head_direction=3&gesture=sml&action=wav&size=l"
                         alt="">
                </a>
            </div>
        </div>
    </div>

    <div class="flex flex-col  ml-[90px] -mt-[55px]">
        <p class="text-2xl font-semibold text-white">
            {{ $user->username }}
        </p>

        <small class="text-gray-200 italic font-semibold">{{ Str::limit($user->motto, 20) ?: 'No motto' }}</small>
    </div>

    <div class="flex w-full items-center justify-end px-4 mt-3">
        <div
            class="min-w-[15px] max-w-[15px] min-h-[15px] max-h-[15px] rounded-full flex items-start {{ $user->online ? 'bg-green-400' : 'bg-red-400' }}">
        </div>
    </div>
</div>
