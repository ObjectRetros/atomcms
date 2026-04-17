<x-app-layout>
    @push('title', __('Staff'))

    <div class="col-span-12 lg:col-span-9 lg:w-[96%]">
        <div class="flex flex-col gap-y-4">
            @forelse($employees as $employee)
                <x-content.staff-content-section :badge="$employee->badge" :color="$employee->staff_color">
                    <x-slot:title>
                        {{ $employee->rank_name }}
                    </x-slot:title>

                    <x-slot:under-title>
                        {{ $employee->job_description }}
                    </x-slot:under-title>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($employee->users as $staff)
                            <x-community.staff-card :user="$staff"/>
                        @endforeach
                    </div>

                    @if($employee->users->isEmpty())
                        <div class="text-center text-sm text-gray-400 dark:text-gray-500 py-2">
                            {{ __('We currently have no staff in this position') }}
                        </div>
                    @endif
                </x-content.staff-content-section>
            @empty
                <div class="p-8 text-center text-gray-400 dark:text-gray-500">
                    {{ __('No staff members found.') }}
                </div>
            @endforelse
        </div>
    </div>

    <div class="col-span-12 lg:col-span-3 lg:w-[110%] space-y-4 lg:-ml-[32px]">
        <x-content.content-section icon="hotel-icon" classes="border dark:border-gray-900">
            <x-slot:title>
                {{ __(':hotel Staff', ['hotel' => setting('hotel_name')]) }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('About the :hotel staff team', ['hotel' => setting('hotel_name')]) }}
            </x-slot:under-title>

            <div class="px-2 text-sm dark:text-gray-200 space-y-3">
                <p>{{ __('The :hotel staff team is one big happy family, each staff member has a different role and duties to fulfill.', ['hotel' => setting('hotel_name')]) }}</p>
                <p>{{ __('Most of our team usually consists of players that have been around :hotel for quite a while, but this does not mean we only recruit old & known players, we recruit those who shine out to us!', ['hotel' => setting('hotel_name')]) }}</p>
            </div>
        </x-content.content-section>

        <x-content.content-section icon="hotel-icon" classes="border dark:border-gray-900">
            <x-slot:title>
                {{ __('Apply for Staff') }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('How to join the staff team', ['hotel' => setting('hotel_name')]) }}
            </x-slot:under-title>

            <div class="px-2 text-sm dark:text-gray-200 space-y-3">
                <p>{{ __('Every now and then staff applications may open up. Once they do we always make sure to post a news article explaining the process.') }}</p>
                <p>
                    {!! __('You can occasionally also look at the :startTag Staff application page :endTag which will show you all of our current open positions.', [
                        'startTag' => '<a href="' . route('staff-applications.index') . '" class="underline font-semibold">',
                        'endTag'   => '</a>',
                    ]) !!}
                </p>
            </div>
        </x-content.content-section>

        {{-- Staff count summary --}}
        <x-content.content-section icon="hotel-icon" classes="border dark:border-gray-900">
            <x-slot:title>
                {{ __('Team Overview') }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('Current staff numbers') }}
            </x-slot:under-title>

            <div class="px-2 space-y-2">
                @foreach($employees as $employee)
                    @if($employee->users->isNotEmpty())
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">{{ $employee->rank_name }}</span>
                        <span class="font-bold text-gray-800 dark:text-gray-200 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-full text-xs">
                            {{ $employee->users->count() }}
                        </span>
                    </div>
                    @endif
                @endforeach
                <div class="pt-2 border-t dark:border-gray-700 flex items-center justify-between text-sm font-bold">
                    <span class="text-gray-700 dark:text-gray-300">{{ __('Total') }}</span>
                    <span class="text-yellow-600 dark:text-yellow-400">
                        {{ $employees->sum(fn($e) => $e->users->count()) }}
                    </span>
                </div>
            </div>
        </x-content.content-section>
    </div>
</x-app-layout>
