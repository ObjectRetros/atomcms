<x-app-layout>
    @push('title', __('Shop'))

    <div class="col-span-12 flex flex-col items-center justify-center py-16 text-center">

        <div class="text-8xl mb-6">🛍️</div>

        <h1 class="text-3xl font-black text-gray-800 dark:text-gray-100 mb-3">
            {{ __('Shop — Coming Soon') }}
        </h1>

        <p class="text-gray-500 dark:text-gray-400 max-w-md mb-8 text-sm leading-relaxed">
            {{ __('We\'re working hard to bring you an amazing shop experience. In the meantime, you can contact us on Discord to make purchases.') }}
        </p>

        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ setting('discord_invitation_link') }}" target="_blank"
               class="inline-flex items-center gap-x-2 px-6 py-3 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03z"/>
                </svg>
                {{ __('Contact us on Discord') }}
            </a>

            <a href="{{ route('welcome') }}"
               class="inline-flex items-center gap-x-2 px-6 py-3 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold text-sm transition">
                {{ __('Back to Home') }}
            </a>
        </div>

        <div class="mt-16 grid grid-cols-1 sm:grid-cols-3 gap-6 max-w-2xl w-full">
            <div class="p-5 rounded-xl bg-white dark:bg-gray-900 shadow text-center">
                <div class="text-3xl mb-2">💎</div>
                <p class="font-bold text-gray-700 dark:text-gray-200 text-sm">{{ __('Diamonds') }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('Coming soon') }}</p>
            </div>
            <div class="p-5 rounded-xl bg-white dark:bg-gray-900 shadow text-center">
                <div class="text-3xl mb-2">🌟</div>
                <p class="font-bold text-gray-700 dark:text-gray-200 text-sm">{{ __('VIP Membership') }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('Coming soon') }}</p>
            </div>
            <div class="p-5 rounded-xl bg-white dark:bg-gray-900 shadow text-center">
                <div class="text-3xl mb-2">🎁</div>
                <p class="font-bold text-gray-700 dark:text-gray-200 text-sm">{{ __('Gift Bundles') }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('Coming soon') }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
