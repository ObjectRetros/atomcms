<x-app-layout>
    @push('title', __('Session logs'))

    <div class="col-span-12 flex flex-col gap-y-0">
        <x-user.settings.settings-navigation />

        <x-content.content-section icon="hotel-icon" classes="border dark:border-gray-900">
            <x-slot:title>
                {{ __('Session logs') }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('Keep an eye on all your active sessions') }}
            </x-slot:under-title>

            <div class="overflow-hidden overflow-x-auto rounded border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr>
                            <th class="whitespace-nowrap dark:text-white px-4 py-2 text-left font-medium text-gray-900">IP</th>
                            <th class="whitespace-nowrap dark:text-white px-4 py-2 text-left font-medium text-gray-900">IP Current Device</th>
                            <th class="whitespace-nowrap dark:text-white px-4 py-2 text-left font-medium text-gray-900">Is Desktop</th>
                            <th class="whitespace-nowrap dark:text-white px-4 py-2 text-left font-medium text-gray-900">{{ __('Platform') }}</th>
                            <th class="whitespace-nowrap dark:text-white px-4 py-2 text-left font-medium text-gray-900">{{ __('Browser') }}</th>
                            <th class="whitespace-nowrap dark:text-white px-4 py-2 text-left font-medium text-gray-900">{{ __('Last Activity') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($logs as $log)
                        <tr>
                            <td class="whitespace-nowrap dark:text-gray-300 px-4 py-2 font-medium text-gray-900">{{ $log->ip_address }}</td>
                            <td class="px-4 py-2 dark:text-gray-300 text-gray-700">{{ $log->is_current_device ? 'true' : 'false' }}</td>
                            <td class="px-4 py-2 dark:text-gray-300 text-gray-700">{{ $log->agent['is_desktop'] ? 'true' : 'false' }}</td>
                            <td class="px-4 py-2 dark:text-gray-300 text-gray-700">{{ $log->agent['platform'] }}</td>
                            <td class="px-4 py-2 dark:text-gray-300 text-gray-700">{{ $log->agent['browser'] }}</td>
                            <td class="whitespace-nowrap dark:text-gray-300 px-4 py-2 text-gray-700">{{ $log->last_active }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td class="whitespace-nowrap text-center dark:text-gray-300 px-4 py-2 text-gray-700" colspan="6">
                                {{ __('No session logs found') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-content.content-section>
    </div>
</x-app-layout>
