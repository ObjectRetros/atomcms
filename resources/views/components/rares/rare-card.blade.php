@props(['rare'])

<div class="p-3 rounded bg-gray-200 dark:bg-gray-700 flex gap-x-6 gap-4 items-center overflow-hidden">
    <div class="w-8 h-8">
        <div class="w-10 h-10 overflow-hidden rounded-full flex items-center justify-center bg-gray-300 dark:bg-gray-800">
            <img src="{{ sprintf('%s/%s', setting('furniture_icons_path'), $rare->furniture_icon) }}" alt="">
        </div>
    </div>
    <div class="flex flex-col w-full">
        <div class="font-bold text-gray-700 dark:text-gray-200 truncate flex items-center gap-x-[5px]">
            @if($rare->item_id)
                <a href="{{ route('values.value', $rare) }}" class="underline">
                    {{ strLimit($rare->name, 15) }}
                </a>
            @else
                {{ strLimit($rare->name, 20) }}
            @endif

            @if($rare->isLimitedEdition())
                <img class="w-4 h-4" src="{{ asset('/assets/images/icons/ltd.png') }}" alt="">
            @endif
        </div>

        <div class="w-full bg-yellow-400 rounded h-[35px] flex items-center mt-2">
            <div class="bg-yellow-500 rounded-l w-1/3 px-4 h-full flex items-center justify-center">
                <img src="{{ asset('assets/images/icons/currency/credits.png') }}" alt="">
            </div>
            <p class="w-full text-center truncate">
                {{ $rare->credit_value ?? 0 }} {{ __('credits') }}
            </p>
        </div>

        <div class="w-full bg-gray-500 rounded h-[35px] flex items-center mt-1">
            <div class="bg-gray-600 rounded-l w-1/3 px-4 h-full flex items-center justify-center">
                <img src="{{ asset('/assets/images/icons/navigation/shop.png') }}" alt="">
            </div>

            <p class="w-full text-center truncate">
                {{ $rare->currency_value ?? 0 }} {{ $rare->currency_type === 0 ? 'Duckets' : ($rare->currency_type === 5 ? 'Diamonds' : 'Other') }}
            </p>
        </div>
    </div>
</div>
