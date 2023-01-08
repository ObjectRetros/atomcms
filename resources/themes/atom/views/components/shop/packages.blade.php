@props(['package','packageContent'])

<x-shop.content-section icon="bronze-vip">
    <x-slot:title>
        {{ $packageContent->name }} -
        @if($package->isOnSale())
            <span class="text-gray-400 line-through">(${{ $package->price }})</span>
            <span> ${{ $package->price() }}</span>
        @else
            ${{ $package->price() }}
        @endif
    </x-slot:title>

    <x-slot:under-title>
        {{ $packageContent->description }}
    </x-slot:under-title>

        @if($package->features)
            <div class="flex flex-col mb-4">
                <p class="font-semibold">{{ __('You will receive:') }}</p>

                <ul class="pl-4 list-disc">
                    @foreach(json_decode($package->features?->content)->features as $feature)
                        <li class="ml-3">{{ $feature }}</li>
                    @endforeach
                </ul>
            </div>
       @endif

        <form action="{{ route('shop.purchase', $package->id) }}" method="POST">
            @csrf

            <x-form.secondary-button>
                {{ __('Purchase') }}
            </x-form.secondary-button>
        </form>
</x-shop.content-section>
