<div class="h-[210px] dark:bg-gray-900 rounded w-full bg-white shadow-sm relative overflow-hidden transition ease-in-out duration-200">
    <div style="background: url('https://i.imgur.com/uGLDOUu.png');" class="article-image">
    </div>

    <div class="mt-4 px-4">
        <p class="font-semibold text-lg truncate dark:text-gray-200">
            {{ __('No published articles') }}
        </p>

        <div class="flex items-center gap-x-2">
            <div
                class="mt-3 flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                <img src="{{ setting('avatar_imager') }}&headonly=1" alt="">
            </div>

            <p class="mt-4 font-semibold dark:text-gray-400">
                {{ setting('hotel_name') }}
            </p>
        </div>
    </div>
</div>
