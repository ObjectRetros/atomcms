<footer
    x-data
    x-on:click="$dispatch('open-modal', 'atom-credits')"
    class="w-full h-14 flex items-center justify-center bg-gray-900 text-gray-500 font-bold cursor-pointer transition duration-200 ease-in-out hover:text-gray-400"
>
    &copy {{ date('Y') }} {{ setting('hotel_name') }} is a not for profit educational project & is in no way affiliated with Sulake Corporation Oy.
</footer>

<x-footer-credits />
