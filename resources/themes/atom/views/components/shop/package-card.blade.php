@props(['package'])

<x-content.shop-card classes="border dark:border-gray-900">
    <x-slot:title>
        {{ $package->name }}
    </x-slot:title>

    <x-slot:under-title>
        {{ $package->description }}
    </x-slot:under-title>

    <div class="flex justify-between dark:text-white">
        <div class="flex flex-col">
            <p class="font-semibold">{{ __('You will receive:') }}</p>

            <ul class="list-disc pl-4">
                @foreach($package->items as $item)
                    <li class="ml-3">{{ $item->pivot->quantity }}x {{ $item->name }}</li>
                @endforeach
            </ul>

            @if($package->stock !== null)
                <p class="mt-2 text-xs text-yellow-500 dark:text-yellow-400">
                    {{ __(':stock remaining', ['stock' => $package->stock]) }}
                </p>
            @endif

            @if($package->limit_per_user)
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Limit: :limit per user', ['limit' => $package->limit_per_user]) }}
                </p>
            @endif
        </div>

        @if($package->image)
            <div class="flex items-start">
                <img src="{{ Storage::url($package->image) }}" alt="" class="max-h-[60px] max-w-[60px]">
            </div>
        @endif
    </div>

    <div class="pt-2 mt-auto flex gap-4">
        @if($package->is_giftable)
            <x-modals.modal-wrapper>
                <div x-on:click="open = true">
                    <x-form.primary-button type="button" classes="px-10">
                        <x-icons.gift />
                    </x-form.primary-button>
                </div>

                <x-modals.regular-modal>
                    <x-slot name="title">
                        <h2 class="text-2xl">
                            {{ __('Gift :package', ['package' => $package->name]) }}
                        </h2>
                    </x-slot>

                    <div class="mt-4">
                        <form action="{{ route('shop.buy-package', $package) }}" method="POST" class="w-full">
                            @csrf

                            <x-form.input name="receiver" type="text" placeholder="Enter the name of the recipient you want to gift" classes="mb-2"/>

                            <button type="submit"
                                    class="w-full rounded bg-green-600 hover:bg-green-700 text-white p-2 border-2 border-green-500 transition ease-in-out duration-150 font-semibold">
                                {{ __('Gift for $:cost', ['cost' => $package->priceInDollars()]) }}
                            </button>
                        </form>
                    </div>
                </x-modals.regular-modal>
            </x-modals.modal-wrapper>
        @endif

        <form action="{{ route('shop.buy-package', $package) }}" method="POST" class="w-full">
            @csrf

            <button type="submit"
                    class="w-full rounded bg-green-600 hover:bg-green-700 text-white p-2 border-2 border-green-500 transition ease-in-out duration-150 font-semibold">
                {{ __('Buy for $:cost', ['cost' => $package->priceInDollars()]) }}
            </button>
        </form>
    </div>
</x-content.shop-card>
