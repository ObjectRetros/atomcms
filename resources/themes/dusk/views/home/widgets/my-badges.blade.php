<div class="p-1">
    <div class="grid grid-cols-4 gap-1">
        @forelse($user->badges as $badge)
            <div class="w-10 h-10 bg-center bg-no-repeat" style="background-image: url('{{ setting('badges_path') }}/{{ $badge->badge_code }}.gif')"></div>
        @empty
            <p class="col-span-4 text-xs text-center text-gray-400">
                {{ __(':username does not have any badges yet.', ['username' => $user->username]) }}
            </p>
        @endforelse
    </div>

    @if($user->badges instanceof \Illuminate\Pagination\LengthAwarePaginator && $user->badges->hasPages())
        <div class="mt-2 text-xs">
            {!! $user->badges->withQueryString()->links() !!}
        </div>
    @endif
</div>
