<x-app-layout>
    @push('title', __('Staff'))
    <div class="col-span-12 lg:col-span-9 lg:w-[96%]">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-2">
            @forelse($positions as $position)
                @continue(!$position->permission) {{-- skip if the rank relation is missing --}}

                <x-content.staff-content-section :badge="$position->permission->badge" :color="$position->permission->staff_color">
                    <x-slot:title>
                        {{ $position->permission->rank_name }}
                    </x-slot:title>
                    <x-slot:under-title>
                        {{ $position->permission->job_description }}
                    </x-slot:under-title>

                    <div class="text-center dark:text-gray-400">
                        <div class="mb-4 text-sm">
                            {!! $position->description !!}
                        </div>
                        <div class="mb-4 text-sm font-semibold">
                            {{ __('Application Deadline :date', [
                                'date' => $position->apply_to ? $position->apply_to->format('F j, Y, g:i A') : __('No deadline set')
                            ]) }}
                        </div>
                    </div>

                    <div class="flex justify-between">
                        @if (auth()->check() && auth()->user()->hasAppliedForPosition($position->permission->id))
                            <x-form.danger-button>
                                {{ __('You have already applied for :position', ['position' => $position->permission->rank_name]) }}
                            </x-form.danger-button>
                        @else
                            <a href="{{ route('staff-applications.show', $position) }}" class="w-full">
                                <x-form.secondary-button>
                                    {{ __('Apply for :position', ['position' => $position->permission->rank_name]) }}
                                </x-form.secondary-button>
                            </a>
                        @endif
                    </div>
                </x-content.staff-content-section>
            @empty
                <x-content.content-card icon="lighthouse-icon" classes="border dark:border-gray-900 col-span-full">
                    <x-slot:title>
                        {{ __('No positions open') }}
                    </x-slot:title>
                    <x-slot:under-title>
                        {{ __('There is currently no positions open') }}
                    </x-slot:under-title>
                    <div class="px-2 text-sm space-y-4 dark:text-gray-200">
                        <p>
                            {{ __('Please come back at a later time to check if we have any positions open by then! Thank you for your interest.', ['hotel' => setting('hotel_name')]) }}
                        </p>
                    </div>
                </x-content.content-card>
            @endforelse
        </div>
    </div>
    <div class="col-span-12 lg:col-span-3 lg:w-[110%] space-y-4 lg:-ml-[32px]">
        <x-content.content-card icon="chat-icon" classes="border dark:border-gray-900">
            <x-slot:title>
                {{ __('Apply for :hotel staff', ['hotel' => setting('hotel_name')]) }}
            </x-slot:title>
            <x-slot:under-title>
                {{ __('Select position to get started', ['hotel' => setting('hotel_name')]) }}
            </x-slot:under-title>
            <div class="px-2 text-sm space-y-4 dark:text-gray-200">
                <p>
                    {{ __('Here at :hotel we open up for staff applications every now and then. Sometimes you will find this page empty other times it might be filled with positions, if you ever come across a position you feel you would fit perfectly into, then do not hesitate to apply for it.', ['hotel' => setting('hotel_name')]) }}
                </p>
            </div>
        </x-content.content-card>
    </div>
</x-app-layout>
