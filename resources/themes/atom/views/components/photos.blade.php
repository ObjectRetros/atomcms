@props(['photos'])

<div class="grid grid-cols-1 gap-2 md:grid-cols-2 lg:grid-cols-4">
    @foreach ($photos as $photo)
        <a href="{{ $photo->url }}" data-fancybox="gallery" class="cursor-pointer">
            <div class="rounded border-2 dark:border-gray-600 h-[280px] relative object-fill overflow-hidden">
                <img class="h-full w-full object-cover object-center" src="{{ $photo->url }}" alt="">
                <div class="absolute bottom-3 left-4 flex items-center gap-x-3 rounded-full bg-white pr-3 dark:bg-gray-800">
                    <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-gray-100 dark:bg-gray-900">
                        <img src="{{ setting('avatar_imager') }}{{ $photo->user->look ?? '' }}&direction=2&headonly=1&head_direction=2&gesture=sml" alt="">
                    </div>

                    <p class="dark:text-white">
                        {{ $photo->user->username ?? __('Unknown') }}
                    </p>
                </div>
            </div>
        </a>
    @endforeach
</div>