<div class="flex flex-col gap-1 p-1">
    @forelse($user->rooms as $room)
        <div class="flex items-center gap-2 py-1 border-b border-gray-600">
            <div @class([
                'w-8 h-8 rounded bg-center bg-no-repeat shrink-0',
                'bg-green-900/50' => in_array($room->state, ['open', 'invisible']),
                'bg-yellow-900/50' => $room->state === 'locked',
                'bg-red-900/50' => $room->state === 'password',
            ])></div>

            <div class="min-w-0">
                <p class="text-xs font-bold text-gray-100 truncate">{{ $room->name }}</p>
                <p class="text-[10px] text-gray-400 truncate">{{ $room->description }}</p>
            </div>
        </div>
    @empty
        <p class="text-xs text-center text-gray-400">
            {{ __(':username does not have any rooms yet.', ['username' => $user->username]) }}
        </p>
    @endforelse
</div>
