<div class="flex flex-nowrap overflow-x-auto gap-x-0 border-b border-gray-200 dark:border-gray-700 mb-5">
    <a href="{{ route('settings.account.show') }}"
       class="flex-shrink-0 px-4 py-2 text-sm flex items-center gap-x-1.5 transition -mb-px whitespace-nowrap
              {{ request()->routeIs('settings.account.show')
                 ? 'border-b-2 border-[#eeb425] text-gray-900 dark:text-white font-semibold'
                 : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
        ⚙️ {{ __('Account') }}
    </a>

    <a href="{{ route('settings.password.show') }}"
       class="flex-shrink-0 px-4 py-2 text-sm flex items-center gap-x-1.5 transition -mb-px whitespace-nowrap
              {{ request()->routeIs('settings.password.show')
                 ? 'border-b-2 border-[#eeb425] text-gray-900 dark:text-white font-semibold'
                 : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
        🔒 {{ __('Password') }}
    </a>

    <a href="{{ route('settings.two-factor') }}"
       class="flex-shrink-0 px-4 py-2 text-sm flex items-center gap-x-1.5 transition -mb-px whitespace-nowrap
              {{ request()->routeIs('settings.two-factor')
                 ? 'border-b-2 border-[#eeb425] text-gray-900 dark:text-white font-semibold'
                 : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
        🔐 {{ __('Two Factor') }}
    </a>

    <a href="{{ route('settings.session-logs') }}"
       class="flex-shrink-0 px-4 py-2 text-sm flex items-center gap-x-1.5 transition -mb-px whitespace-nowrap
              {{ request()->routeIs('settings.session-logs')
                 ? 'border-b-2 border-[#eeb425] text-gray-900 dark:text-white font-semibold'
                 : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
        📋 {{ __('Session Logs') }}
    </a>
</div>
