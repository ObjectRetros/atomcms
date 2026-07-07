@props([
    'name',
    'title' => null,
    'maxWidth' => 'lg',
])

@php
    $panelWidth = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
    ][$maxWidth] ?? 'sm:max-w-lg';

    // Themes without a light mode render shared components in their dark variant.
    $alwaysDarkTheme = in_array(setting('theme'), ['dusk'], true);
@endphp

{{--
    Open from anywhere with: $dispatch('open-modal', '{{ $name }}')
    Close with: $dispatch('close-modal', '{{ $name }}'), the button, ESC or the backdrop.
--}}
<div
    x-data="{ open: false }"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') open = true"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') open = false"
    x-on:keydown.escape.window="open = false"
>
    <template x-teleport="body">
        <div
            x-show="open"
            @class(['dark' => $alwaysDarkTheme, 'fixed inset-0 z-[90] flex items-end justify-center p-4 sm:items-center'])
            style="display: none"
            role="dialog"
            aria-modal="true"
            @if ($title) aria-label="{{ $title }}" @endif
        >
            <div
                x-show="open"
                x-transition.opacity.duration.200ms
                x-on:click="open = false"
                class="fixed inset-0 bg-gray-950/60 backdrop-blur-sm"
            ></div>

            <div
                x-show="open"
                x-trap.noscroll="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="translate-y-4 opacity-0 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 sm:scale-100"
                x-transition:leave-end="opacity-0 sm:scale-95"
                class="relative flex max-h-[85vh] w-full {{ $panelWidth }} flex-col overflow-hidden rounded-xl bg-white shadow-2xl dark:bg-gray-800"
            >
                <div class="flex items-center justify-between gap-4 border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $title }}
                    </h3>

                    <button
                        type="button"
                        class="rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-200"
                        x-on:click="open = false"
                        aria-label="{{ __('Close') }}"
                    >
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                        </svg>
                    </button>
                </div>

                <div class="overflow-y-auto px-5 py-4">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </template>
</div>
