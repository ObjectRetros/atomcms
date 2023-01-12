<x-app-layout>
    @push('title', __('Rare values'))

    <div class="col-span-12 lg:col-span-9 lg:w-[96%]">
        <div class="flex flex-col gap-y-4">
            @foreach($categories as $category)
                <x-content.content-section icon="camera-icon">
                    <x-slot:title>
                        {{ $category->name }}
                    </x-slot:title>

                    <x-slot:under-title>
                        All the {{ $category->name }} rares
                    </x-slot:under-title>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($category->furniture as $rare)
                            <div class="p-3 rounded bg-gray-200 dark:bg-gray-700 flex gap-x-6 gap-4 items-center overflow-hidden">
                                <div class="w-10 h-10">
                                    <div class="w-10 h-10 overflow-hidden rounded-full flex items-center justify-center bg-gray-300 dark:bg-gray-800">
                                        <img src="{{ sprintf('%s/%s', setting('rare_values_icons_path'), $rare->furniture_icon) }}" alt="">
                                    </div>

                                </div>
                                <div class="flex flex-col">
                                    <p class="font-bold text-gray-700 dark:text-gray-200 truncate">{{ $rare->name }}</p>

                                    <p class="text-sm text-gray-600 dark:text-gray-200">
                                        <span class="font-semibold">
                                            Credits Value:
                                        </span>

                                        {{ $rare->credit_value ?? 0 }}
                                    </p>

                                    <p class="text-sm text-gray-600 dark:text-gray-200">
                                        <span class="font-semibold">
                                            Value:
                                        </span>

                                        {{ $rare->currency_value ?? 0 }} {{ $rare->currency_value_type }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-content.content-section>
            @endforeach
        </div>
    </div>

    <div class="col-span-12 lg:col-span-3 lg:w-[110%] space-y-4 lg:-ml-[32px]">
        <x-content.content-section icon="hotel-icon" classes="border dark:border-gray-900">
            <x-slot:title>
                {{ __('Search') }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('Search for rares') }}
            </x-slot:under-title>

            <form action="{{ route('values.search') }}" method="POST" class="space-y-3">
                @csrf

                <x-form.input name="search" placeholder="Search for a rare"/>

                <x-form.secondary-button>
                    {{ __('Search') }}
                </x-form.secondary-button>
            </form>
        </x-content.content-section>

        <x-content.content-section icon="hotel-icon" classes="border dark:border-gray-900">
            <x-slot:title>
                {{ __('Rare categories') }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('Select a category below') }}
            </x-slot:under-title>

            <div class="px-2 text-sm dark:text-gray-200 space-y-2">
                <div class="rounded bg-gray-200 dark:bg-gray-700 py-2 px-4 transition duration-200 ease-in-out hover:scale-[102%]">
                    <a href="{{ route('values.index') }}" class="block text">
                        All values
                    </a>
                </div>

                @foreach($categoriesNav as $category)
                    <div class="rounded bg-gray-200 dark:bg-gray-700 py-2 px-4 transition duration-200 ease-in-out hover:scale-[102%]">
                        <a href="{{ route('values.category', $category->id) }}" class="block text">
                            {{ $category->name }}
                        </a>
                    </div>
                @endforeach
            </div>
        </x-content.content-section>
    </div>
</x-app-layout>
