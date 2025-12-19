@props(['getUrl' => null, 'fallbackUrl' => null])

<div class="flex items-center gap-3">
    <div class="text-sm text-gray-600 dark:text-gray-300">Current icon:</div>
    <img
        src="{{ is_callable($getUrl) ? $getUrl() : $getUrl }}"
        alt=""
        class="h-8 w-8 object-contain"
        loading="lazy"
        onerror="this.onerror=null;this.src='{{ $fallbackUrl }}';"
        style="image-rendering: pixelated; image-rendering: crisp-edges;"
    />
</div>
