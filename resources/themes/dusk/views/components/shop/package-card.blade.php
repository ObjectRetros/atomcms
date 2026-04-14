@props(['package'])

<x-content.shop-card>
    <x-slot:title>
        <div class="flex justify-between w-full">
            <p>
                {{ $package->name }}
            </p>

            <span class="font-bold">
                ${{ $package->priceInDollars() }}
            </span>
        </div>
    </x-slot:title>

    <div class="flex flex-col dark:text-white w-full">
        @if($package->image)
            <div class="flex justify-center w-full">
                <div class="p-2 max-w-[65px] max-h-[65px]">
                    <img src="{{ Storage::url($package->image) }}" alt="">
                </div>
            </div>
        @endif

        @if($package->description)
            <p class="text-gray-100 text-sm mt-2">{{ $package->description }}</p>
        @endif

        <div class="mt-3">
            <p class="font-semibold text-sm mb-1">{{ __('Includes:') }}</p>
            <ul class="list-disc pl-4 text-sm text-gray-300">
                @foreach($package->items as $item)
                    <li class="ml-1">{{ $item->pivot->quantity }}x {{ $item->name }}</li>
                @endforeach
            </ul>
        </div>

        @if($package->stock !== null)
            <div class="mt-2 text-xs text-yellow-400">
                {{ __(':stock remaining', ['stock' => $package->stock]) }}
            </div>
        @endif

        @if($package->limit_per_user)
            <div class="mt-1 text-xs text-gray-400">
                {{ __('Limit: :limit per user', ['limit' => $package->limit_per_user]) }}
            </div>
        @endif
    </div>

    @auth
        <div class="pt-4 mt-auto flex gap-4">
            <div class="w-full flex gap-2">
                @if($package->is_giftable)
                    <x-modals.modal-wrapper>
                        <div x-on:click="open = true">
                            <x-form.primary-button classes="!text-blue-100 px-4 w-full !bg-[#0b80b3] !border-[#1891c4] hover:!bg-[#096891] transition-all">
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
            </div>

            <form action="{{ route('shop.buy-package', $package) }}" method="POST">
                @csrf

                <x-form.secondary-button type="submit" classes="text-green-100 px-4">
                    {{ __('Buy') }}
                </x-form.secondary-button>
            </form>
        </div>
    @endauth
</x-content.shop-card>
