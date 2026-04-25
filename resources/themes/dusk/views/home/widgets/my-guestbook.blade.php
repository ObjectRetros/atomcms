<div class="flex flex-col gap-2 p-1">
    @auth
        @if(auth()->id() !== $user->id)
            <form data-home-message-form class="flex flex-col gap-1 border-b border-gray-600 pb-2">
                <textarea
                    name="content"
                    maxlength="500"
                    rows="2"
                    class="w-full rounded border border-gray-600 bg-[#21242e] p-2 text-xs text-gray-200"
                    placeholder="{{ __('Leave a message') }}"
                    data-no-drag
                    required
                ></textarea>
                <button type="submit" class="self-end rounded bg-blue-600 px-3 py-1 text-xs font-semibold text-white hover:bg-blue-500" data-no-drag>
                    {{ __('Post') }}
                </button>
            </form>
        @endif
    @endauth

    @forelse($user->receivedHomeMessages as $message)
        <div class="flex gap-2 border-b border-gray-600 pb-2">
            <img
                class="w-8 h-14 object-cover shrink-0"
                style="image-rendering: pixelated; object-position: -9px -7px;"
                src="{{ setting('avatar_imager') }}{{ $message->user->look }}&direction=4&head_direction=4&action=sml&size=s"
                alt="{{ $message->user->username }}"
            >

            <div class="flex flex-col min-w-0">
                <div class="flex items-center gap-1">
                    @if($message->user->online)
                        <span class="w-2 h-2 bg-green-400 rounded-full shrink-0"></span>
                    @else
                        <span class="w-2 h-2 bg-gray-500 rounded-full shrink-0"></span>
                    @endif

                    <a class="text-xs font-semibold text-blue-400 hover:underline truncate" href="{{ route('profile.show', $message->user) }}">
                        {{ $message->user->username }}
                    </a>
                </div>

                <div class="text-xs text-gray-300 mt-1 max-h-[100px] overflow-y-auto">
                    {{ $message->renderedContent }}
                </div>

                <span class="text-[10px] text-gray-500 mt-1">
                    {{ $message->created_at->diffForHumans() }}
                </span>
            </div>
        </div>
    @empty
        <p class="text-xs text-center text-gray-400">
            {{ __(":username hasn't received any messages yet.", ['username' => $user->username]) }}
        </p>
    @endforelse
</div>
