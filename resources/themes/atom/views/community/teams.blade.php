<x-app-layout>
    @push('title', __('Staff'))

    <div 
        x-data="{ q: '', hideEmpty: false }"
        class="col-span-12 space-y-4"
    >
        {{-- Toolbar --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-2xl font-semibold tracking-tight">{{ __('Teams') }}</h1>

            <div class="flex w-full flex-col items-stretch gap-3 sm:w-auto sm:flex-row sm:items-center">
                <div class="relative sm:w-80">
                    <input
                        x-model="q"
                        type="text"
                        placeholder="{{ __('Search teams…') }}"
                        class="w-full rounded-xl border border-gray-200 px-4 py-2.5 pr-10 text-sm focus:border-[#eeb425] focus:outline-none focus:ring-0 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                    />
                    <svg class="pointer-events-none absolute right-3 top-1/2 h-5 w-5 -translate-y-1/2 opacity-60"
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="m21 21-4.35-4.35M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16z" />
                    </svg>
                </div>

                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" x-model="hideEmpty"
                           class="rounded border-gray-300 text-[#eeb425] focus:ring-[#eeb425]" />
                    <span class="select-none">{{ __('Hide empty teams') }}</span>
                </label>
            </div>
        </div>

        {{-- Teams --}}
        <div class="flex flex-col gap-y-4">
            @forelse ($employees as $employee)
                @php
                    /** @var \Illuminate\Support\Collection $users */
                    $users = $employee->users ?? collect();
                    $memberCount = $users->count();
                    $searchText = trim(($employee->rank_name ?? '') . ' ' . ($employee->job_description ?? ''));
                @endphp

                <section
                    x-data="{ 
                        name: @js($searchText), 
                        hasMembers: {{ $memberCount > 0 ? 'true' : 'false' }} 
                    }"
                    x-show="(name.toLowerCase().includes(q.toLowerCase())) && (!hideEmpty || hasMembers)"
                    x-cloak
                >
                    <x-content.staff-content-section 
                        :badge="$employee->badge" 
                        :color="$employee->staff_color"
                        class="overflow-hidden rounded-2xl border border-gray-100 shadow-sm dark:border-gray-800"
                    >
                        {{-- Header --}}
                        <div class="flex items-start justify-between">
                            <div>
                                <x-slot:title>
                                    {{ $employee->rank_name }}
                                </x-slot:title>
                                <x-slot:under-title>
                                    {{ $employee?->job_description }}
                                </x-slot:under-title>
                            </div>

                            {{-- Member count chip --}}
                            <span class="ml-4 shrink-0 rounded-full border border-gray-200 px-3 py-1 text-xs font-medium dark:border-gray-700">
                                {{ $memberCount }} {{ \Illuminate\Support\Str::plural(__('member'), $memberCount) }}
                            </span>
                        </div>

                        {{-- Members grid --}}
                        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                            @forelse ($users as $staff)
                                <x-community.staff-card :user="$staff" />
                            @empty
                                <div class="col-span-full">
                                    <div class="rounded-xl border border-dashed border-gray-200 p-6 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                        {{ __('We currently have no staff in this team') }}
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </x-content.staff-content-section>
                </section>
            @empty
                <x-content.content-card icon="lighthouse-icon" classes="border dark:border-gray-900">
                    <x-slot:title>{{ __('No teams found') }}</x-slot:title>
                    <x-slot:under-title>{{ __('Please check back later.') }}</x-slot:under-title>
                    <div class="px-2 text-sm space-y-4 dark:text-gray-200">
                        <p>{{ __('There are no teams to display right now.') }}</p>
                    </div>
                </x-content.content-card>
            @endforelse
        </div>
    </div>
</x-app-layout>
