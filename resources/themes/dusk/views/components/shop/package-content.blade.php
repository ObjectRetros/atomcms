@props(['package', 'gift' => false])

<x-modals.regular-modal>
    <x-slot name="title">
        <h2 class="text-2xl">
            {{ __(':package contents', ['package' => $package->name]) }}
        </h2>
    </x-slot>

    <ul class="list-disc pl-4">
        @if($package->features)
            @foreach($package->features as $feature)
                <li class="ml-3">
                    {{ $feature->content }}
                </li>
            @endforeach
        @endif

        @if ($package->credits)
            <li class="ml-3">{{ number_format($package->credits, 0, '.', '.') }} credits</li>
        @endif

        @if ($package->duckets)
            <li class="ml-3">{{ number_format($package->duckets, 0, '.', '.') }} duckets</li>
        @endif

        @if ($package->diamonds)
            <li class="ml-3">{{ number_format($package->diamonds, 0, '.', '.') }} diamonds</li>
        @endif

        @if ($package->rank)
            <li class="ml-3">
                {{ $package->rank->rank_name }} rank
            </li>
        @endif
    </ul>

    <div class="mt-6">
        <p class="font-bold">
            {{ __('Other features:') }}
        </p>

        <div class="flex flex-col gap-3 text-gray-100 mt-3 bg-[#303642] p-4 rounded-md">
            @if (!empty($package->badges))
                <p>
                    {{ __('Badge(s) included:') }}
                </p>

                <div class="flex flex-wrap gap-2 items-center">
                    @foreach (explode(';', $package->badges) as $badge)
                        <div
                            class="h-[50px] w-[50px] overflow-hidden p-2 bg-[#444d5c] rounded-md flex items-center justify-center">
                            <img data-tippy-content="1x {{ $badge }}"
                                 src="{{ setting('badges_path') }}/{{$badge}}.gif" alt="{{ $badge }}"
                                 style="image-rendering: auto;">
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($package->furniture)
                <p>
                    {{ __('Furniture included:') }}
                </p>
                <div class="flex flex-col dark:text-white">
                    <div class="flex flex-wrap gap-2 items-center">
                        @foreach ($package->furniItems() as $furni)
                            <div
                                class="h-[50px] w-[50px] overflow-hidden p-2 bg-[#444d5c] rounded-md flex items-center justify-center">
                                <img
                                    data-tippy-content="{{ collect(json_decode($package->furniture))->firstWhere('item_id', $furni->id)->amount }}x {{ $furni->public_name }}"
                                    src="{{$furni->icon()}}" alt="{{ __('Missing icon') }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-4">
        <form action="{{ route('shop.buy', $package) }}" method="POST" class="w-full">
            @csrf

            @if($gift)
                <x-form.input name="receiver" type="text" placeholder="Enter the name of the recipient you want to gift" classes="mb-2"/>
            @endif

            <button type="submit"
                    class="w-full rounded bg-green-600 hover:bg-green-700 text-white p-2 border-2 border-green-500 transition ease-in-out duration-150 font-semibold">
                {{ __('Buy for $:cost', ['cost' => $package->price()]) }}
            </button>
        </form>
    </div>
</x-modals.regular-modal>
