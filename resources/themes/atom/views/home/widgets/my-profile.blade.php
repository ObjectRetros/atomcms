<div class="flex flex-col gap-2 p-2">
    <div class="flex items-start gap-3 border-b border-gray-200 dark:border-gray-600 pb-3">
        <div class="flex flex-col">
            <a class="font-bold text-blue-500 hover:underline" href="{{ route('profile.show', $user) }}">
                {{ $user->username }}
            </a>

            @if($user->online)
                <span class="text-xs text-green-500 font-semibold">{{ __('Online') }}</span>
            @else
                <span class="text-xs text-gray-400">{{ __('Offline') }}</span>
            @endif

            <span class="text-xs text-gray-500 mt-1">
                {{ __('Member since') }}
                {{ date('Y-m-d', $user->account_created) }}
            </span>
        </div>

        <img
            class="w-16 h-auto ml-auto"
            style="image-rendering: pixelated;"
            src="{{ setting('avatar_imager') }}{{ $user->look }}&direction=4&head_direction=4&size=m&gesture=sml"
            alt="{{ $user->username }}"
        >
    </div>

    <p class="text-xs italic text-gray-600 dark:text-gray-300">{{ $user->motto }}</p>
</div>
