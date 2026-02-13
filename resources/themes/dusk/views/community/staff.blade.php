<x-app-layout>
    @push('title', __('Staff'))

    <div class="col-span-12 lg:col-span-8">
        <div class="flex flex-col gap-y-5">
            @foreach ($employees as $employee)
                <x-content.staff-content-section
                    :badge="$employee->badge"
                    :color="$employee->staff_color"
                    :count="count($employee->users)"
                >
                    <x-slot:title>
                        {{ $employee->rank_name }}
                    </x-slot:title>

                    <x-slot:under-title>
                        {{ $employee->job_description }}
                    </x-slot:under-title>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($employee->users as $staff)
                            <x-community.staff-card :user="$staff" />
                        @endforeach
                    </div>

                    @if (count($employee->users) === 0)
                        <div class="rounded-lg border border-gray-700 bg-[#252a36] px-4 py-6 text-center text-sm text-gray-300">
                            {{ __('We currently have no staff in this position') }}
                        </div>
                    @endif
                </x-content.staff-content-section>
            @endforeach
        </div>
    </div>

    <div class="col-span-12 space-y-4 lg:col-span-4 xl:col-span-3 lg:sticky lg:top-6 lg:self-start">
        <x-content.content-card icon="chat-icon" classes="border border-gray-900">
            <x-slot:title>
                {{ __(':hotel staff', ['hotel' => setting('hotel_name')]) }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('About the :hotel staff', ['hotel' => setting('hotel_name')]) }}
            </x-slot:under-title>

            <div class="px-1 text-sm space-y-4 text-gray-200 leading-relaxed">
                <p>
                    {{ __('The :hotel staff team is one big happy family, each staff member has a different role and duties to fulfill.', ['hotel' => setting('hotel_name')]) }}
                </p>

                <p>
                    {{ __('Most of our team usually consists of players that have been around :hotel for quite a while, but this does not mean we only recruit old & known players, we recruit those who shine out to us!', ['hotel' => setting('hotel_name')]) }}
                </p>
            </div>
        </x-content.content-card>

        <x-content.content-card icon="chat-icon" classes="border border-gray-900">
            <x-slot:title>
                {{ __('Apply for staff') }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('How to join the staff team', ['hotel' => setting('hotel_name')]) }}
            </x-slot:under-title>

            <div class="px-1 text-sm space-y-4 text-gray-200 leading-relaxed">
                <p>
                    {{ __('Every now and then staff applications may open up. Once they do we always make sure to post a news article explaining the process - So make sure you keep an eye out for those in you are interested in joining the :hotel staff team.', ['hotel' => setting('hotel_name')]) }}
                </p>

                <p>
                    {!! __(
                        'You can occasionally also look at the :startTag Staff application page :endTag which will show you all of our current open positions.',
                        ['startTag' => '<a href="/community/staff-applications" class="underline">', 'endTag' => '</a>'],
                    ) !!}
                </p>
            </div>
        </x-content.content-card>
    </div>
</x-app-layout>
