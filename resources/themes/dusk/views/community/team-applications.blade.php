<x-app-layout>
    @push('title', __('Staff'))

    <div class="col-span-12 lg:col-span-9 lg:w-[96%]">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-2">
            @forelse($positions as $position)
                @continue(!$position->team)

                <x-content.staff-content-section :badge="$position->team->badge" :color="$position->team->staff_color">
                    <x-slot:title>
                        {{ $position->team->rank_name }}
                    </x-slot:title>

                    <x-slot:under-title>
                        {{ $position->team?->job_description }}
                    </x-slot:under-title>

                    <div class="text-center dark:text-gray-400">
                        <div class="mb-4 text-sm">
                            {!! $position->description !!}
                        </div>
                        <div class="mb-4 text-sm font-semibold">
                            {{ __('Application Deadline :date', ['date' => $position->apply_to ? $position->apply_to->format('F j, Y, g:i A') : __('No deadline set')]) }}
                        </div>
                    </div>

                    <div class="flex justify-between">
                        @auth
                            @if (auth()->user()->hasAppliedForTeam($position->team->id))
                                <x-form.danger-button class="w-full justify-center">
                                    {{ __('You have already applied for :position', ['position' => $position->team->rank_name]) }}
                                </x-form.danger-button>
                            @else
                                <a href="{{ route('team-applications.show', $position) }}" class="w-full">
                                    <x-form.secondary-button class="w-full justify-center">
                                        {{ __('Apply for :position', ['position' => $position->team->rank_name]) }}
                                    </x-form.secondary-button>
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="w-full">
                                <x-form.secondary-button class="w-full justify-center">
                                    {{ __('Login to apply') }}
                                </x-form.secondary-button>
                            </a>
                        @endauth
                    </div>
                </x-content.staff-content-section>
            @empty
                <x-content.content-card icon="lighthouse-icon" classes="border dark:border-gray-900 col-span-full">
                    <x-slot:title>{{ __('No team positions open') }}</x-slot:title>
                    <x-slot:under-title>{{ __('There are currently no open team positions') }}</x-slot:under-title>
                    <div class="px-2 text-sm space-y-4 dark:text-gray-200">
                        <p>{{ __('Please come back later to check for new openings. Thank you!') }}</p>
                    </div>
                </x-content.content-card>
            @endforelse
        </div>
    </div>

    <div class="col-span-12 lg:col-span-3 lg:w-[110%] space-y-4 lg:-ml-[32px]">
        <x-content.content-card icon="chat-icon" classes="border dark:border-gray-900">
            <x-slot:title>{{ __('Apply for :hotel Team', ['hotel' => setting('hotel_name')]) }}</x-slot:title>
            <x-slot:under-title>{{ __('Select a team to get started') }}</x-slot:under-title>
            <div class="px-2 text-sm space-y-4 dark:text-gray-200">
                <p>{{ __('We open team applications periodically. If you see a team you fit, do not hesitate to apply!') }}</p>
            </div>
        </x-content.content-card>
    </div>
</x-app-layout>
