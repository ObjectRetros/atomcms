@props(['icons' => []])

<div x-data="{ open: false }" class="mt-6">
    <div class="flex items-center justify-between mb-2">
        <h3 class="text-base font-medium">Icon picker</h3>
        <button
            type="button"
            class="fi-btn fi-btn-size-md fi-btn-color-gray fi-btn-variant-outline"
            @click="open = !open"
        >
            <span x-text="open ? 'Hide icons' : 'Select icon'"></span>
        </button>
    </div>

    <template x-if="open">
        <div
            class="grid gap-2 mt-2"
            style="grid-template-columns: repeat(auto-fill, minmax(48px, 1fr));"
        >
            @foreach($icons as $icon)
                <button
                    type="button"
                    class="rounded border p-1 bg-white dark:bg-gray-900 flex items-center justify-center border-gray-200 dark:border-gray-700 hover:border-primary-400"
                    @click="$wire.setIconFromPicker({{ $icon['id'] }})"
                    title="Icon {{ $icon['id'] }}"
                >
                    <img
                        src="{{ $icon['url'] }}"
                        alt="icon {{ $icon['id'] }}"
                        class="h-8 w-8 object-contain"
                        loading="lazy"
                        onerror="this.onerror=null;this.src='{{ $icon['fallback'] }}';"
                        style="image-rendering: pixelated; image-rendering: crisp-edges;"
                    />
                </button>
            @endforeach
        </div>
    </template>
</div>
