<x-app-layout>
    @push('title', __('Rules'))

    <div class="col-span-12 space-y-6">
        <x-page-header>
            <x-slot:icon>
                <img src="{{ asset('/assets/images/dusk/exclamation-mark_icon.png') }}" alt="">
            </x-slot:icon>

            {{ __('Rules') }}
        </x-page-header>

        @foreach ($categories as $category)
            <x-content.content-card icon="{{ $category->badge }}">
                <x-slot:title>
                    {{ $category->name }}
                </x-slot:title>

                <x-slot:under-title>
                    {{ $category->description }}
                </x-slot:under-title>

                <ul class="text-gray-300 space-y-1">
                    @foreach ($category->rules as $rule)
                        <li><strong>{{ $rule->paragraph }}.</strong> {{ $rule->rule }}</li>
                    @endforeach
                </ul>
            </x-content.content-card>
        @endforeach
    </div>
</x-app-layout>
