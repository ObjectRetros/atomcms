<x-app-layout>
    @push('title', __('Leaderboard'))

    <div class="col-span-12">
        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
            <x-leaderboard-card title="{{ __('Top credits') }}" icon="credits.png" :data="$credits" valueType="Credits" />
            <x-leaderboard-card title="{{ __('Top duckets') }}" icon="duckets.png" :data="$duckets" valueType="Duckets" />
            <x-leaderboard-card title="{{ __('Top diamonds') }}" icon="diamond.png" :data="$diamonds" valueType="Diamonds" />
            <x-leaderboard-card title="{{ __('Hours online') }}" icon="clock.gif" :data="$mostOnline" valueType="Hours online" :formatValue="fn($value) => round($value / 3600)" />
            <x-leaderboard-card title="{{ __('Respects received') }}" icon="heart.gif" :data="$respectsReceived" valueType="Respect received" />
            <x-leaderboard-card title="{{ __('Achievement score') }}" icon="star.gif" :data="$achievementScores" valueType="Achievement points" />
        </div>
    </div>
</x-app-layout>
