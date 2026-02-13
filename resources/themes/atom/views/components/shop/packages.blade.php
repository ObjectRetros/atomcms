<x-content.shop-card icon-url="{{ $article->icon_url }}" color="{{ $article->color }}" classes="border dark:border-gray-900">
    <x-slot:title>
        {{ $article->name }}
    </x-slot:title>

    <x-slot:under-title>
        {{ $article->info }}
    </x-slot:under-title>

    <div class="flex justify-between dark:text-white">

        <div class="flex flex-col">
            <p class="font-semibold">{{ __('You will receive:') }}</p>

            <ul class="list-disc pl-4">
                @if($article->features)
                    @foreach($article->features as $feature)
                        <li class="ml-3">
                            {{ $feature->content }}
                        </li>
                    @endforeach
                @endif


                @if ($article->credits)
                    <li class="ml-3">{{ number_format($article->credits, 0, '.', '.') }} credits</li>
                @endif

                @if ($article->duckets)
                    <li class="ml-3">{{ number_format($article->duckets, 0, '.', '.') }} duckets</li>
                @endif

                @if ($article->diamonds)
                    <li class="ml-3">{{ number_format($article->diamonds, 0, '.', '.') }} diamonds</li>
                @endif

                @if ($article->rank)
                    <li class="ml-3">
                        {{ $article->rank->rank_name }} rank
                    </li>
                @endif

                @if ($article->furniture)
                    @foreach ($article->furniItems() as $furni)
                        <li class="ml-3">
                            {{ collect(json_decode($article->furniture))->firstWhere('item_id', $furni->id)->amount }}
                            x {{ $furni->public_name }}
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>

        <div class="flex flex-col gap-3">
            @if (!empty($article->badges))
                <div class="flex flex-col items-end">
                    Badge(s):
                    <div class="flex flex-col dark:text-white py-1.5 px-2 rounded bg-gray-200 dark:bg-gray-700">
                        <div class="flex gap-2 items-center">
                            @foreach (explode(';', $article->badges) as $badge)
                                <img data-tippy-content="1x {{ $badge }}" class="user-badge"
                                     src="{{ setting('badges_path') }}/{{$badge}}.png" alt="{{ $badge }}"
                                     style="image-rendering: auto;">
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if ($article->furniture)
                <div class="flex flex-col items-end">
                    Furniture:
                    <div class="flex flex-col dark:text-white py-2 px-4 rounded bg-gray-200 dark:bg-gray-700">
                        <div class="flex gap-2 items-center">
                            @foreach ($article->furniItems() as $furni)
                                <div>
                                    <img
                                        data-tippy-content="{{ collect(json_decode($article->furniture))->firstWhere('item_id', $furni->id)->amount }}x {{ $furni->public_name }}"
                                        class="user-badge" src="{{$furni->icon()}}" alt="{{ $furni->public_name }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="pt-2 mt-auto flex gap-4">
        @if($article->is_giftable)
            <x-modals.modal-wrapper>
                <div x-on:click="open = true">
                    <x-form.primary-button type="button" classes="px-10">
                        <x-icons.gift />
                    </x-form.primary-button>
                </div>

                <x-modals.regular-modal>
                    <x-slot name="title">
                        <h2 class="text-2xl">
                            {{ __('Gift :package', ['package' => $article->name]) }}
                        </h2>
                    </x-slot>

                    <div class="mt-4">
                        <form action="{{ route('shop.buy', $article) }}" method="POST" class="w-full">
                            @csrf

                            <x-form.input name="receiver" type="text" placeholder="Enter the name of the recipient you want to gift" classes="mb-2"/>

                            <button type="submit"
                                    class="w-full rounded bg-green-600 hover:bg-green-700 text-white p-2 border-2 border-green-500 transition ease-in-out duration-150 font-semibold">
                                {{ __('Gift for $:cost', ['cost' => $article->price()]) }}
                            </button>
                        </form>
                    </div>
                </x-modals.regular-modal>
            </x-modals.modal-wrapper>
        @endif

        <form action="{{ route('shop.buy', $article) }}" method="POST" class="w-full">
            @csrf

            <button type="submit"
                    class="w-full rounded bg-green-600 hover:bg-green-700 text-white p-2 border-2 border-green-500 transition ease-in-out duration-150 font-semibold">
                {{ __('Buy for $:cost', ['cost' => $article->price()]) }}
            </button>
        </form>
    </div>
</x-content.shop-card>
