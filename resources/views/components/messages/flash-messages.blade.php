@php
    // Server-side flashes become the toast stack's initial entries.
    $initialToasts = [];

    if (session()->has('message')) {
        $initialToasts[] = ['title' => session('message'), 'icon' => 'error'];
    }

    foreach ($errors->all() as $error) {
        $initialToasts[] = ['title' => $error, 'icon' => 'error'];
    }

    if ($errors->hasBag('login')) {
        foreach ($errors->getBag('login')->all() as $error) {
            $initialToasts[] = ['title' => $error, 'icon' => 'error'];
        }
    }

    if (session()->has('success')) {
        $initialToasts[] = ['title' => session('success'), 'icon' => 'success'];
    }
@endphp

<div
    x-data="toastHub(@js($initialToasts))"
    x-on:toast.window="push($event.detail.title, $event.detail.icon)"
    @class([
        // Themes without a light mode render shared components in their dark variant.
        'dark' => in_array(setting('theme'), ['dusk'], true),
        'pointer-events-none fixed right-4 top-4 z-[100] flex w-80 max-w-[calc(100vw-2rem)] flex-col gap-2',
    ])
    role="status"
    aria-live="polite"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-4 opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @class([
                'toast-card pointer-events-auto relative flex items-start gap-3 overflow-hidden p-3 pr-9 shadow-lg',
                // Match each theme's card anatomy.
                'rounded-lg bg-[#2b303c]' => setting('theme') === 'dusk',
                'rounded border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800' => setting('theme') !== 'dusk',
            ])
        >
            <template x-if="toast.icon === 'success'">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                </svg>
            </template>
            <template x-if="toast.icon === 'error'">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-rose-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd" />
                </svg>
            </template>
            <template x-if="toast.icon !== 'success' && toast.icon !== 'error'">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-sky-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" />
                </svg>
            </template>

            <p class="text-sm font-medium text-gray-800 dark:text-gray-100" x-text="toast.title"></p>

            <button
                type="button"
                class="absolute right-2 top-2 rounded p-1 text-gray-400 transition hover:text-gray-600 dark:hover:text-gray-200"
                x-on:click="remove(toast.id)"
                aria-label="{{ __('Dismiss') }}"
            >
                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                </svg>
            </button>

            {{-- The progress bar drives dismissal; hovering the card pauses it. --}}
            <div
                class="toast-progress absolute bottom-0 left-0 h-0.5"
                :class="{
                    'bg-emerald-500': toast.icon === 'success',
                    'bg-rose-500': toast.icon === 'error',
                    'bg-sky-500': toast.icon !== 'success' && toast.icon !== 'error',
                }"
                x-on:animationend="remove(toast.id)"
            ></div>
        </div>
    </template>
</div>

<style>
    @keyframes toast-progress {
        from { width: 100%; }
        to { width: 0; }
    }

    .toast-progress {
        animation: toast-progress 4s linear forwards;
    }

    .toast-card:hover .toast-progress {
        animation-play-state: paused;
    }
</style>

<script>
    (() => {
        // Site-wide toast API. Accepts the same icons the old SweetAlert
        // mixin did: success, error, or anything else for an info style.
        window.toast = (title, icon = 'success') => {
            window.dispatchEvent(new CustomEvent('toast', { detail: { title, icon } }));
        };

        // Compatibility shim for the previous SweetAlert-based call sites.
        window.Toast = {
            fire: ({ title = '', icon = 'success' } = {}) => window.toast(title, icon),
        };

        // A plain global so Alpine can resolve it from x-data regardless of
        // how the theme bundles Alpine.
        window.toastHub = (initialToasts = []) => ({
            toasts: [],
            nextId: 1,

            init() {
                initialToasts.forEach((toast) => this.push(toast.title, toast.icon));
            },

            push(title, icon = 'success') {
                if (! title) {
                    return;
                }

                this.toasts.push({ id: this.nextId++, title, icon });
            },

            remove(id) {
                this.toasts = this.toasts.filter((toast) => toast.id !== id);
            },
        });
    })();
</script>
