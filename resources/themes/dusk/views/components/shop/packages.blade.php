<x-content.shop-card color="{{ $article->color }}">
    <x-slot:title>
        <div class="flex justify-between w-full">
            <p>
                {{ $article->name }}
            </p>

            <span class="font-bold">
                ${{ $article->price() }}
            </span>
        </div>
    </x-slot:title>

    <div class="flex justify-between dark:text-white w-full">
        <div class="flex flex-col items-center w-full">
            <div class="flex justify-center w-full">
                <div class="p-2 max-w-[65px] max-h-[65px]">
                    <img src="{{ $article->icon_url }}" alt="">
                </div>
            </div>

            <div class="text-gray-100 mt-4">
                {{ $article->info }}
            </div>
        </div>
    </div>

    <div class="pt-4 mt-auto flex gap-4">
        <div class="w-full flex gap-2">
            <x-modals.modal-wrapper>
                <div x-on:click="open = true">
                    <x-form.primary-button classes="px-4 w-full !text-yellow-100">
                        <x-icons.eye />
                    </x-form.primary-button>
                </div>


                <x-shop.package-content :package="$article"/>
            </x-modals.modal-wrapper>

            @if($article->is_giftable)
                <x-modals.modal-wrapper>
                    <div x-on:click="open = true">
                        <x-form.primary-button classes="!text-blue-100 px-4 w-full !bg-[#0b80b3] !border-[#1891c4] hover:!bg-[#096891] transition-all">
                            <x-icons.gift />
                        </x-form.primary-button>
                    </div>

                    <x-shop.package-content :package="$article" :gift="true"/>
                </x-modals.modal-wrapper>
            @endif
        </div>

        <form action="{{ route('shop.buy', $article) }}" method="POST">
            @csrf

            <x-form.secondary-button type="submit" classes="text-green-100 px-4">
                {{ __('Buy') }}
            </x-form.secondary-button>
        </form>
    </div>
</x-content.shop-card>
