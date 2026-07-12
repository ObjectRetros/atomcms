<footer
    x-data
    x-on:click="$dispatch('open-modal', 'atom-credits')"
    class="mt-auto flex h-14 w-full flex-col items-center justify-center bg-gray-100 text-sm text-gray-400 dark:bg-gray-900 md:flex-row md:justify-center md:px-8"
>
    <div class="md:font-semibold text-[12px] md:text-[14px] cursor-pointer hover:underline">
        &copy {{ date('Y') }} -
        {{ __(':hotel is a not for profit educational project', ['hotel' => setting('hotel_name')]) }}
    </div>
</footer>

<x-footer-credits />
