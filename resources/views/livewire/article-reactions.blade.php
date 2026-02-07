@php($theme = setting('theme', 'dusk'))

<div class="mt-4" wire:keydown.escape.window="closeModal">
    <div class="flex w-full flex-wrap gap-2 rounded-lg p-2 {{ $theme === 'dusk' ? 'bg-gray-900 text-gray-100' : 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100' }}">
        @if ($isAuthenticated)
            <button
                type="button"
                class="px-2 hover:scale-110 transition-all font-semibold h-8 flex items-center justify-center border-2 text-xs border-yellow-400 cursor-pointer bg-[#eeb425] text-white rounded-lg"
                wire:click="openModal"
            >
                {{ __('Add') }}
            </button>
        @endif

        @foreach ($articleReactions as $reaction)
            @php($popoverId = 'article-reaction-' . \Illuminate\Support\Str::slug($reaction['name']))
            <div class="relative" wire:key="reaction-{{ $reaction['name'] }}">
                <button
                    type="button"
                    class="flex h-8 w-12 items-center justify-center gap-2 rounded-lg border-2 text-sm font-bold {{ $theme === 'dusk' ? 'border-gray-800 text-gray-100' : 'border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100' }} hover:bg-gray-700 cursor-pointer"
                    @class([
                        'bg-gray-800 border-gray-700 text-gray-100' => $myReactions->contains($reaction['name']),
                        'cursor-pointer hover:scale-110 transition-all hover:bg-gray-700' => $isAuthenticated,
                    ])
                    wire:click="toggleReaction('{{ $reaction['name'] }}')"
                    data-popover-target="{{ $popoverId }}"
                    data-popover-trigger="hover"
                    data-popover-placement="top"
                >
                    <img
                        src="/assets/images/icons/reactions/{{ $reaction['name'] }}.png"
                        alt="{{ $reaction['name'] }}"
                    >
                    <span>{{ $reaction['count'] }}</span>
                </button>

                <div
                    data-popover
                    id="{{ $popoverId }}"
                    role="tooltip"
                    class="invisible absolute z-10 inline-block w-64 rounded-lg border text-sm font-light opacity-0 shadow-xs transition-opacity duration-300 border-gray-600 bg-gray-800 text-gray-100"
                >
                    <div class="rounded-t-lg border-b px-3 py-2 border-gray-600 bg-gray-700">
                        <div class="flex w-full items-center justify-center font-semibold text-white">
                            {{ __('Reactions with') }}
                            <img src="/assets/images/icons/reactions/{{ $reaction['name'] }}.png" class="ml-1" alt="{{ $reaction['name'] }}">
                        </div>
                    </div>
                    <div class="overflow-y-auto px-3 py-2" style="max-height: 200px">
                        @foreach ($reaction['users'] as $username)
                            <p class="w-full text-center">{{ $username }}</p>
                        @endforeach
                    </div>
                    <div data-popper-arrow></div>
                </div>
            </div>
        @endforeach
    </div>

    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70" wire:click.self="closeModal">
            <div class="w-full max-w-lg rounded-lg {{ $theme === 'dusk' ? 'bg-gray-900 text-gray-100' : 'bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100' }} p-4 shadow-lg">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl">{{ __('Insert Reaction') }}</h2>
                    <button type="button" class="text-gray-300 hover:text-white" wire:click="closeModal">
                        ✕
                    </button>
                </div>

                <div class="mt-4 flex w-full flex-wrap justify-center gap-3">
                    @foreach ($availableReactions as $reaction)
                        <button
                            type="button"
                            class="cursor-pointer rounded-lg border-2 px-3 py-2 {{ $theme === 'dusk' ? 'border-gray-800 hover:bg-gray-700' : 'border-gray-300 dark:border-gray-700 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                            wire:click="toggleReaction('{{ $reaction }}')"
                            wire:key="available-reaction-{{ $reaction }}"
                        >
                            <img
                                src="/assets/images/icons/reactions/{{ $reaction }}.png"
                                alt="{{ $reaction }}"
                            >
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

@once
    @push('javascript')
        <script>
            const initReactionPopovers = () => {
                document.dispatchEvent(new CustomEvent('reactions:loaded'));
            };

            document.addEventListener('DOMContentLoaded', initReactionPopovers);
            document.addEventListener('livewire:initialized', initReactionPopovers);
            document.addEventListener('livewire:navigated', initReactionPopovers);
        </script>
    @endpush
@endonce
