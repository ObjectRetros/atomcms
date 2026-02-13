@props(['user'])

<div class="relative h-24 w-full overflow-hidden rounded-lg bg-[#171a23] md:mt-0">
    <div class="h-[65%] w-full staff-bg"
        style="background: rgba(0, 0, 0, 0.6) url({{ asset(sprintf('assets/images/%s', $user->permission->staff_background)) }});">
    </div>

    <span
        class="absolute right-2 top-2 inline-flex items-center gap-1 rounded-full border border-black/30 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide {{ $user->online ? 'bg-green-400/20 text-green-200' : 'bg-red-400/20 text-red-200' }}"
        title="{{ $user->online ? __('Online') : __('Offline') }}"
    >
        <span class="inline-block h-2 w-2 rounded-full {{ $user->online ? 'bg-green-400' : 'bg-red-400' }}"></span>
        {{ $user->online ? __('Online') : __('Offline') }}
    </span>

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

    @if ($user->badges->isNotEmpty())
        <div class="absolute bottom-2 left-[90px] right-2">
            <div class="flex flex-nowrap items-center gap-1 overflow-hidden whitespace-nowrap">
                @foreach ($user->badges->take(3) as $badge)
                    <span class="inline-flex h-[45px] w-[45px] shrink-0 items-center justify-center rounded border border-[#4a5060] bg-[#343a4a]">
                        <img
                            src="{{ setting('badges_path') }}/{{ $badge->badge_code }}.gif"
                            alt="{{ $badge->badge_code }}"
                            class="h-[45px] w-[45px] object-contain"
                        >
                    </span>
                @endforeach
            </div>
        </div>
    @endif

</div>
