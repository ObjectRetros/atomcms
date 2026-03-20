<div class="flex flex-col gap-1 p-1">
    @if($user->friends->isNotEmpty())
        <div class="grid grid-cols-2 gap-1.5">
            @foreach($user->friends as $friend)
                <div class="flex items-center gap-2 p-1 bg-gray-50 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600">
                    <img
                        class="w-8 h-14 object-cover"
                        style="image-rendering: pixelated; object-position: -7px -7px;"
                        src="{{ setting('avatar_imager') }}{{ $friend->user->look }}&direction=4&head_direction=4&action=sml&size=s"
                        alt="{{ $friend->user->username }}"
                    >

                    <div class="overflow-hidden">
                        <a class="block text-xs font-semibold text-blue-500 hover:underline truncate" href="{{ route('profile.show', $friend->user) }}">
                            {{ $friend->user->username }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        @if($user->friends instanceof \Illuminate\Pagination\LengthAwarePaginator && $user->friends->hasPages())
            <div class="mt-2 text-xs">
                {!! $user->friends->withQueryString()->links() !!}
            </div>
        @endif
    @else
        <p class="text-xs text-center text-gray-500">
            {{ __(':username does not have any friends yet.', ['username' => $user->username]) }}
        </p>
    @endif
</div>
