<x-app-layout>
    @push('title', 'Create a ticket')

    <x-content.content-card icon="chat-icon" classes="border dark:border-gray-900 dark:text-gray-100 col-span-12">
        <x-slot:title>
           {{ __('All tickets') }}
        </x-slot:title>

        <div class="overflow-hidden overflow-x-auto rounded border border-gray-200 dark:border-gray-700">
            <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-800">
                <tr>
                    <th class="whitespace-nowrap px-4 py-2 text-left font-medium text-gray-900 dark:text-white">
                        {{ __('Title') }}
                    </th>
                    <th class="whitespace-nowrap px-4 py-2 text-left font-medium text-gray-900 dark:text-white">
                        {{ __('Author') }}
                    </th>
                    <th class="whitespace-nowrap px-4 py-2 text-left font-medium text-gray-900 dark:text-white">
                        {{ __('Status') }}
                    </th>
                    <th class="whitespace-nowrap px-4 py-2 text-left font-medium text-gray-900 dark:text-white">
                        {{ __('Actions') }}
                    </th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($tickets as $ticket)
                    <tr>
                        <td class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-gray-300">
                            {{ Str::limit($ticket->title, 80) }}
                        </td>
                        <td class="px-4 py-2 text-gray-700 dark:text-gray-300">
                            {{ $ticket->user->username }}</td>
                        <td class="px-4 py-2 text-gray-700 dark:text-gray-300">
                            {{ $ticket->open ? 'Open' : 'Closed' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-gray-300 flex gap-x-3">
                            <a data-turbolinks="false" href="{{ route('help-center.ticket.show', $ticket) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>

                            <a data-turbolinks="false" href="{{ route('help-center.ticket.edit', $ticket) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </a>

                            @if(hasPermission('delete_website_tickets'))
                                <form action="{{ route('help-center.ticket.destroy', $ticket) }}" method="POST">
                                    @method('DELETE')
                                    @csrf

                                    <button type="submit">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="whitespace-nowrap px-4 py-2 text-center text-gray-700 dark:text-gray-300"
                            colspan="3">
                            {{ __('No tickets available') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $tickets->links() }}
        </div>
    </x-content.content-card>
</x-app-layout>
