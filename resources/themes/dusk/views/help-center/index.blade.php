<x-app-layout>
    @push('title', auth()->user()->username)

    <div class="col-span-12 flex flex-col lg:flex-row gap-4">
        <div class="flex flex-col gap-4 w-full lg:w-3/5">
            @foreach($categories->where('small_box', false) as $category)
                <x-content.content-card icon="duo-chat-icon" classes="border border-gray-900">
                    <x-slot:title>
                        {{ $category->name }}
                    </x-slot:title>

                    <div class="px-2 text-sm text-gray-200">
                        <img class="px-2" style="float: right !important;"
                             src="{{ asset('/assets/images/help-center/' . $category->image_url) }}" alt="">
                        {!! $category->content !!}
                    </div>

                    <a data-turbolinks="false" href="{{ $category->button_url ?? '#' }}" class="mt-4 ml-2">
                        <button
                            style="background-color: {{ $category->button_color }}; border: {{ $category->button_border_color }} solid 2px;"
                            class="px-2 py-1 text-white font-semibold rounded transition hover:scale-105">
                            {{ $category->button_text }}
                        </button>
                    </a>
                </x-content.content-card>
            @endforeach
        </div>

        <div class="flex flex-col gap-4 w-full lg:w-2/5">
            @foreach($categories->where('small_box', true) as $category)
                <x-content.content-card icon="duo-chat-icon" classes="border border-gray-900">
                    <x-slot:title>
                        {{ $category->name }}
                    </x-slot:title>

                    <div class="px-2 text-sm text-gray-200">
                        {!! $category->content !!}
                    </div>

                    <a data-turbolinks="false" href="{{ $category->button_url ?? '#' }}" class="mt-4 ml-2">
                        <button
                            style="background-color: {{ $category->button_color }}; border: {{ $category->button_border_color }} solid 2px;"
                            class="px-2 py-1 text-white font-semibold rounded transition hover:scale-105">
                            {{ $category->button_text }}
                        </button>
                    </a>
                </x-content.content-card>
            @endforeach
        </div>
    </div>
</x-app-layout>
