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

    @auth
        <div class="pt-2 mt-auto flex gap-4" x-data="{
            confirmOpen: false,
            confirmAction: '',
            confirmReceiver: '',
            confirmMessage: '',
        }">
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

                        <div class="mt-4" x-data="{
                            query: '',
                            results: [],
                            activeIndex: -1,
                            showDropdown: false,
                            loading: false,
                            debounceTimer: null,
                            search() {
                                clearTimeout(this.debounceTimer);
                                if (this.query.length < 2) {
                                    this.results = [];
                                    this.showDropdown = false;
                                    return;
                                }
                                this.loading = true;
                                this.debounceTimer = setTimeout(async () => {
                                    const response = await fetch(`/api/users/search?q=${encodeURIComponent(this.query)}`);
                                    this.results = await response.json();
                                    this.showDropdown = this.results.length > 0;
                                    this.activeIndex = -1;
                                    this.loading = false;
                                }, 250);
                            },
                            select(username) {
                                this.query = username;
                                this.showDropdown = false;
                                this.activeIndex = -1;
                            },
                            handleKeydown(event) {
                                if (!this.showDropdown) return;
                                if (event.key === 'ArrowDown') {
                                    event.preventDefault();
                                    this.activeIndex = (this.activeIndex + 1) % this.results.length;
                                } else if (event.key === 'ArrowUp') {
                                    event.preventDefault();
                                    this.activeIndex = this.activeIndex <= 0 ? this.results.length - 1 : this.activeIndex - 1;
                                } else if (event.key === 'Enter' && this.activeIndex >= 0) {
                                    event.preventDefault();
                                    this.select(this.results[this.activeIndex].username);
                                } else if (event.key === 'Tab' && this.activeIndex >= 0) {
                                    event.preventDefault();
                                    this.select(this.results[this.activeIndex].username);
                                } else if (event.key === 'Escape') {
                                    this.showDropdown = false;
                                    this.activeIndex = -1;
                                }
                            },
                        }">
                            <form x-ref="giftForm" action="{{ route('shop.buy-package', $package) }}" method="POST" class="w-full"
                                  x-on:submit.prevent="
                                      if (!query.trim()) return;
                                      confirmAction = '{{ route('shop.buy-package', $package) }}';
                                      confirmReceiver = query;
                                      confirmMessage = '{{ __('You are about to gift :package to', ['package' => $package->name]) }} ' + query + '. {{ __('You will be charged $:cost. This action is non-refundable.', ['cost' => $package->priceInDollars()]) }}';
                                      open = false;
                                      $nextTick(() => { confirmOpen = true; });
                                  ">
                                @csrf

                                <div class="relative">
                                    <input type="text"
                                           x-model="query"
                                           x-on:input="search()"
                                           x-on:keydown="handleKeydown($event)"
                                           x-on:click.outside="showDropdown = false"
                                           name="receiver"
                                           placeholder="{{ __('Search for a user...') }}"
                                           autocomplete="off"
                                           class="mb-2 focus:ring-0 border-4 border-gray-200 rounded dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 focus:border-[#eeb425] w-full"
                                           role="combobox"
                                           aria-expanded="showDropdown"
                                           aria-autocomplete="list"
                                    />

                                    <div x-show="loading && query.length >= 2" class="absolute right-3 top-3">
                                        <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                    </div>

                                    <ul x-show="showDropdown"
                                        x-transition.opacity
                                        class="absolute z-50 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow-lg max-h-60 overflow-y-auto"
                                        role="listbox">
                                        <template x-for="(user, index) in results" :key="user.username">
                                            <li x-on:click="select(user.username)"
                                                x-on:mouseenter="activeIndex = index"
                                                :class="{ 'bg-blue-100 dark:bg-gray-700': activeIndex === index }"
                                                class="flex items-center gap-3 px-3 py-2 cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors"
                                                role="option"
                                                :aria-selected="activeIndex === index">
                                                <img :src="'{{ setting('avatar_imager') }}' + user.look + '&direction=2&head_direction=3&gesture=sml&headonly=1&size=s'"
                                                     alt="" class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-600">
                                                <span x-text="user.username" class="text-sm font-medium dark:text-gray-200"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>

                                <button type="submit"
                                        :disabled="!query.trim()"
                                        :class="{ 'opacity-50 cursor-not-allowed': !query.trim() }"
                                        class="w-full rounded bg-green-600 hover:bg-green-700 text-white p-2 border-2 border-green-500 transition ease-in-out duration-150 font-semibold">
                                    {{ __('Gift for $:cost', ['cost' => $package->priceInDollars()]) }}
                                </button>
                            </form>
                        </div>
                    </x-modals.regular-modal>
                </x-modals.modal-wrapper>
            @endif

            <form x-ref="buyForm" action="{{ route('shop.buy-package', $package) }}" method="POST" class="w-full"
                  x-on:submit.prevent="
                      confirmAction = '{{ route('shop.buy-package', $package) }}';
                      confirmReceiver = '';
                      confirmMessage = '{{ __('You are about to purchase :package for $:cost. This action is non-refundable. Are you sure?', ['package' => $package->name, 'cost' => $package->priceInDollars()]) }}';
                      confirmOpen = true;
                  ">
                @csrf

                <button type="submit"
                        class="w-full rounded bg-green-600 hover:bg-green-700 text-white p-2 border-2 border-green-500 transition ease-in-out duration-150 font-semibold">
                    {{ __('Buy for $:cost', ['cost' => $package->priceInDollars()]) }}
                </button>
            </form>

            <div x-show="confirmOpen" style="display: none" x-on:keydown.escape.prevent.stop="confirmOpen = false" role="dialog" aria-modal="true" class="fixed inset-0 z-[60] overflow-y-auto">
                <div x-show="confirmOpen" x-transition x-on:click="confirmOpen = false"
                    class="relative flex min-h-screen items-center justify-center overflow-hidden p-4">
                    <div x-show="confirmOpen" x-transition.opacity class="fixed inset-0 bg-black/50"></div>

                    <div x-on:click.stop x-trap.noscroll.inert="confirmOpen"
                        class="relative w-full max-w-md rounded bg-white px-6 py-6 text-black shadow-md dark:bg-gray-900 dark:text-gray-200">

                        <div class="flex flex-col items-center text-center">
                            <svg class="w-12 h-12 text-yellow-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold mb-2">{{ __('Confirm Purchase') }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6" x-text="confirmMessage"></p>
                        </div>

                        <form :action="confirmAction" method="POST" class="flex gap-3">
                            @csrf
                            <input type="hidden" name="receiver" :value="confirmReceiver">

                            <button type="button" x-on:click="confirmOpen = false"
                                    class="flex-1 rounded bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 p-2 border-2 border-gray-300 dark:border-gray-600 transition ease-in-out duration-150 font-semibold">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit"
                                    class="flex-1 rounded bg-green-600 hover:bg-green-700 text-white p-2 border-2 border-green-500 transition ease-in-out duration-150 font-semibold">
                                {{ __('Confirm') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endauth
</x-content.shop-card>
