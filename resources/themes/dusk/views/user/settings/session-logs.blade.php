<x-app-layout>
    @push('title', __('Session logs'))

    <div class="col-span-12 flex flex-col gap-y-3 md:col-span-3">
        <x-user.settings.settings-navigation />
    </div>

    <div class="col-span-12 flex flex-col gap-y-3 md:col-span-9">
        <x-content.content-card icon="hotel-icon">
            <x-slot:title>
                {{ __('Session logs') }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('Keep an eye on all your active sessions') }}
            </x-slot:under-title>

            <div class="overflow-hidden overflow-x-auto rounded border-2 border-gray-700">
                <table class="min-w-full text-sm divide-y divide-gray-700">
                    <thead class="bg-[#21242e]">
                        <tr>
                            <th class="whitespace-nowrap px-4 py-2 text-left font-medium text-gray-200">
                                {{ __('IP') }}
                            </th>
                            <th class="whitespace-nowrap px-4 py-2 text-left font-medium text-gray-200">
                                {{ __('IP Current Device') }}
                            </th>
                            <th class="whitespace-nowrap px-4 py-2 text-left font-medium text-gray-200">
                                {{ __('Is Desktop') }}
                            </th>
                            <th class="whitespace-nowrap px-4 py-2 text-left font-medium text-gray-200">
                                {{ __('Platform') }}
                            </th>
                            <th class="whitespace-nowrap px-4 py-2 text-left font-medium text-gray-200">
                                {{ __('Browser') }}
                            </th>
                            <th class="whitespace-nowrap px-4 py-2 text-left font-medium text-gray-200">
                                {{ __('Last Activity') }}
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($logs as $log)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-2 font-medium text-gray-200">
                                    {{ $log->ipAddress }}
                                </td>
                                <td class="px-4 py-2 text-gray-200">
                                    {{ $log->isCurrentDevice ? 'true' : 'false' }}</td>
                                <td class="px-4 py-2 text-gray-200">
                                    {{ $log->agent['is_desktop'] ? 'true' : 'false' }}</td>
                                <td class="px-4 py-2 text-gray-200">{{ $log->agent['platform'] }}
                                </td>
                                <td class="px-4 py-2 text-gray-200">{{ $log->agent['browser'] }}</td>
                                <td class="whitespace-nowrap px-4 py-2 text-gray-200">
                                    {{ $log->lastActive }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="whitespace-nowrap px-4 py-2 text-center text-gray-200"
                                    colspan="3">
                                    {{ __('No session logs found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-content.content-card>
    </div>
</x-app-layout>
