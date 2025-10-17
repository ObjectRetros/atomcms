<div x-show="open" style="display: none" x-on:keydown.escape.prevent.stop="open = false" role="dialog" aria-modal="true"
    x-id="['modal-title']" :aria-labelledby="$id('modal-title')" class="fixed inset-0 z-50 overflow-y-auto">
    {{-- Overlay --}}
    <div x-show="open" x-transition x-on:click="open = false"
        class="relative flex min-h-screen items-center justify-center overflow-hidden p-4">
        <div x-show="open" x-transition.opacity class="fixed inset-0 bg-black/50"></div>

        <div x-on:click.stop x-trap.noscroll.inert="open"
            class="relative w-full max-w-xl rounded bg-white px-6 py-6 text-black shadow-md dark:bg-gray-900 dark:text-gray-200 lg:max-w-2xl lg:px-8">
            <button type="button" x-on:click="open = false"
                class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white">
                <svg aria-hidden="true" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="sr-only">{{ __('Close modal') }}</span>
            </button>

            {{-- Title --}}
            <div class="mb-2 flex flex-col items-center" :id="$id('modal-title')">
                {{ $title }}
            </div>

            {{-- Content --}}
            {{ $slot }}
        </div>
    </div>
</div>
