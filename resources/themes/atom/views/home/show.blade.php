<x-app-layout>
    @push('title', __('Home of :u', ['u' => $user->username]))

    <div class="col-span-12 flex flex-col items-center gap-4" x-data="homeManager('{{ $user->username }}', {{ $isMe ? 'true' : 'false' }})">

        {{-- Toolbar --}}
        @if($isMe)
            <div class="w-full max-w-[928px]">
                <template x-if="editing">
                    <div class="flex justify-between items-center">
                        <div class="flex gap-2">
                            <button class="border-2 border-blue-400 bg-blue-500 hover:bg-blue-600 text-white font-semibold text-sm px-4 py-1.5 rounded transition" @click="openBag('inventory')" x-show="!saving">{{ __('Inventory') }}</button>
                            <button class="border-2 border-yellow-400 bg-[#eeb425] hover:bg-[#d49f1c] text-white font-semibold text-sm px-4 py-1.5 rounded transition" @click="openBag('shop')" x-show="!saving">{{ __('Shop') }}</button>
                        </div>
                        <div class="flex gap-2">
                            <button class="border-2 border-red-400 bg-red-500 hover:bg-red-600 text-white font-semibold text-sm px-4 py-1.5 rounded transition" @click="cancel()" x-show="!saving">{{ __('Cancel') }}</button>
                            <button class="border-2 border-green-500 bg-green-600 hover:bg-green-700 text-white font-semibold text-sm px-4 py-1.5 rounded transition disabled:opacity-50" @click="save()" :disabled="saving">
                                <span x-show="!saving">{{ __('Save') }}</span>
                                <span x-show="saving">{{ __('Saving...') }}</span>
                            </button>
                        </div>
                    </div>
                </template>
                <template x-if="!editing">
                    <button class="border-2 border-yellow-400 bg-[#eeb425] hover:bg-[#d49f1c] text-white font-semibold text-sm px-5 py-1.5 rounded transition" @click="editing = true">{{ __('Edit Home') }}</button>
                </template>
            </div>
        @else
            <h2 class="text-xl font-semibold dark:text-white">{{ __('Home of :u', ['u' => $user->username]) }}</h2>
        @endif

        {{-- Canvas --}}
        <div
            class="home-canvas relative w-full max-w-[928px] h-[1360px] border dark:border-gray-700 border-gray-300 rounded-lg overflow-hidden bg-cover bg-center bg-no-repeat shadow-sm"
            :style="{ backgroundImage: `url(${bg()})` }"
        >
            <template x-for="item in visible()" :key="item.id">
                <div
                    class="absolute select-none"
                    :class="{ 'cursor-grab active:cursor-grabbing': editing, 'ring-2 ring-[#eeb425] rounded': selectedItem?.id === item.id }"
                    :style="{ left: (item.x||0)+'px', top: (item.y||0)+'px', zIndex: item.z||0, transform: item.is_reversed ? 'scaleX(-1)' : '' }"
                    :data-home-item="item.id"
                    @click="select(item)"
                >
                    <template x-if="item.home_item?.type === 's'">
                        <img :src="item.home_item.image" class="max-w-none pointer-events-none" draggable="false">
                    </template>
                    <template x-if="item.home_item?.type === 'n'">
                        <div class="p-3 min-w-[150px] min-h-[80px] bg-amber-50 border border-amber-200 rounded shadow text-xs text-gray-800 pointer-events-none" x-html="item.parsed_data || item.extra_data || ''"></div>
                    </template>
                    <template x-if="item.home_item?.type === 'w'">
                        <div class="min-w-[270px] max-w-[300px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow overflow-hidden pointer-events-none">
                            <div class="bg-gray-50 dark:bg-gray-900 px-3 py-2 font-semibold text-sm" x-text="item.home_item.name"></div>
                            <div class="p-2 text-sm" x-html="item.content || ''"></div>
                        </div>
                    </template>
                    <template x-if="editing && selectedItem?.id === item.id">
                        <button data-no-drag class="absolute top-0 right-0 w-7 h-7 bg-red-500/90 hover:bg-red-400 text-white rounded-bl-lg text-xs flex items-center justify-center backdrop-blur-sm z-50" @click.stop="remove(item)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        </button>
                    </template>
                </div>
            </template>
        </div>

        {{-- Bag Modal --}}
        <template x-if="showBag">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="showBag = false">
                <div class="absolute inset-0 bg-black/40" @click="showBag = false"></div>
                <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-2xl w-full max-w-[820px] flex flex-col overflow-hidden" style="max-height: 75vh" @click.stop>

                    {{-- Tabs --}}
                    <div class="flex items-center bg-gray-50 dark:bg-gray-900 border-b dark:border-gray-700 px-3 py-2 gap-1 shrink-0">
                        <button class="px-4 py-1.5 rounded text-sm font-semibold transition" :class="bagTab === 'inventory' ? 'bg-blue-500 text-white' : 'text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700 dark:text-gray-400'" @click="bagTab = 'inventory'; fetchInv()">{{ __('Inventory') }}</button>
                        <button class="px-4 py-1.5 rounded text-sm font-semibold transition" :class="bagTab === 'shop' ? 'bg-[#eeb425] text-white' : 'text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700 dark:text-gray-400'" @click="bagTab = 'shop'; fetchShopCats()">{{ __('Shop') }}</button>
                        <button class="ml-auto text-gray-400 hover:text-gray-700 dark:hover:text-white text-lg leading-none px-2" @click="showBag = false">&times;</button>
                    </div>

                    <div class="flex flex-1 min-h-0">

                        {{-- INVENTORY --}}
                        <template x-if="bagTab === 'inventory'">
                            <div class="flex w-full min-h-0">
                                <div class="w-44 shrink-0 border-r dark:border-gray-700 p-2 flex flex-col gap-0.5">
                                    <template x-for="t in ['stickers','notes','widgets','backgrounds']" :key="t">
                                        <button class="text-left px-3 py-1.5 rounded text-sm capitalize transition" :class="invTab === t ? 'bg-blue-500 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700'" @click="invTab = t; invActive = null; placeQty = 1" x-text="t"></button>
                                    </template>
                                </div>
                                <div class="flex-1 p-3 overflow-y-auto">
                                    <template x-if="invLoading"><p class="text-gray-400 text-sm text-center py-10">{{ __('Loading...') }}</p></template>
                                    <template x-if="!invLoading">
                                        <div class="flex flex-wrap gap-1.5">
                                            <template x-for="item in invItems()" :key="item.home_item_id">
                                                <div class="w-16 h-16 border rounded flex items-center justify-center cursor-pointer relative transition" :class="invActive?.home_item_id === item.home_item_id ? 'border-[#eeb425] bg-yellow-50 dark:bg-[#eeb425]/10' : 'border-gray-200 dark:border-gray-600 hover:border-gray-400'" @click="invActive = item; placeQty = 1" @dblclick="quickPlace(item)">
                                                    <img :src="item.home_item?.image" class="max-w-[56px] max-h-[56px] object-contain" :style="invTab === 'backgrounds' ? 'image-rendering: pixelated' : ''">
                                                    <span x-show="item.item_ids?.length > 1" class="absolute -top-1 -right-1 bg-blue-500 text-white text-[9px] rounded-full min-w-[16px] h-4 flex items-center justify-center px-0.5" x-text="item.item_ids?.length"></span>
                                                </div>
                                            </template>
                                            <template x-if="invItems().length === 0"><p class="text-gray-400 text-sm w-full text-center py-10">{{ __('No items here.') }}</p></template>
                                        </div>
                                    </template>
                                </div>
                                <div class="w-44 shrink-0 border-l dark:border-gray-700 p-3 flex flex-col">
                                    <template x-if="invActive">
                                        <div class="flex flex-col items-center gap-2 text-center">
                                            <p class="font-semibold text-sm" x-text="invActive.home_item?.name"></p>
                                            <img :src="invActive.home_item?.image" class="max-w-[72px] max-h-[72px] object-contain">
                                            <p class="text-xs text-gray-400"><span x-text="invActive.item_ids?.length"></span> {{ __('available') }}</p>
                                            <template x-if="invActive.home_item?.type === 's' && invActive.item_ids?.length > 1">
                                                <input type="number" x-model.number="placeQty" min="1" :max="Math.min(15, invActive.item_ids?.length)" class="w-16 border dark:border-gray-600 dark:bg-gray-700 rounded px-2 py-1 text-sm text-center">
                                            </template>
                                            <button class="w-full border-2 border-green-500 bg-green-600 hover:bg-green-700 text-white rounded font-semibold text-sm py-1.5 transition mt-auto" @click="place()">{{ __('Place') }}</button>
                                        </div>
                                    </template>
                                    <template x-if="!invActive"><p class="text-gray-400 text-xs text-center mt-8">{{ __('Select an item to place it on your home.') }}</p></template>
                                </div>
                            </div>
                        </template>

                        {{-- SHOP --}}
                        <template x-if="bagTab === 'shop'">
                            <div class="flex w-full min-h-0">
                                <div class="w-44 shrink-0 border-r dark:border-gray-700 p-2 flex flex-col gap-0.5 overflow-y-auto">
                                    <button class="text-left px-3 py-1.5 rounded text-sm transition" :class="shopTab === 'notes' ? 'bg-[#eeb425] text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700'" @click="setShopTab('notes')">{{ __('Notes') }}</button>
                                    <button class="text-left px-3 py-1.5 rounded text-sm transition" :class="shopTab === 'widgets' ? 'bg-[#eeb425] text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700'" @click="setShopTab('widgets')">{{ __('Widgets') }}</button>
                                    <button class="text-left px-3 py-1.5 rounded text-sm transition" :class="shopTab === 'backgrounds' ? 'bg-[#eeb425] text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700'" @click="setShopTab('backgrounds')">{{ __('Backgrounds') }}</button>
                                    <div class="border-t dark:border-gray-700 mt-1 pt-1">
                                        <p class="text-[10px] text-gray-400 uppercase tracking-wider px-3 mb-1">{{ __('Stickers') }}</p>
                                        <template x-for="cat in shopCategories" :key="cat.id">
                                            <button class="text-left px-3 py-1 rounded text-sm w-full flex items-center gap-1.5 transition truncate" :class="shopTab === 'cat-'+cat.id ? 'bg-[#eeb425] text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700'" @click="openCat(cat.id)">
                                                <img :src="cat.icon" class="w-4 h-4 shrink-0 object-contain">
                                                <span x-text="cat.name" class="truncate"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                <div class="flex-1 p-3 overflow-y-auto">
                                    <template x-if="shopTab === 'home'"><p class="text-gray-400 text-sm text-center py-10">{{ __('Pick a category to browse items.') }}</p></template>
                                    <template x-if="shopTab !== 'home'">
                                        <div>
                                            <template x-if="shopLoading"><p class="text-gray-400 text-sm text-center py-10">{{ __('Loading...') }}</p></template>
                                            <template x-if="!shopLoading">
                                                <div class="flex flex-wrap gap-1.5">
                                                    <template x-for="item in shopItems" :key="item.id">
                                                        <div class="w-16 h-16 border rounded flex items-center justify-center cursor-pointer relative transition" :class="shopActive?.id === item.id ? 'border-[#eeb425] bg-yellow-50 dark:bg-[#eeb425]/10' : 'border-gray-200 dark:border-gray-600 hover:border-gray-400'" @click="pickShop(item)">
                                                            <img :src="item.image" class="max-w-[56px] max-h-[56px] object-contain" :style="item.type === 'b' ? 'image-rendering: pixelated' : ''">
                                                            <span class="absolute bottom-0 right-0 bg-black/60 text-[9px] text-white px-1 rounded-tl leading-tight" x-text="item.price"></span>
                                                        </div>
                                                    </template>
                                                    <template x-if="shopItems.length === 0"><p class="text-gray-400 text-sm w-full text-center py-10">{{ __('No items.') }}</p></template>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                                <div class="w-44 shrink-0 border-l dark:border-gray-700 p-3 flex flex-col">
                                    <template x-if="shopActive">
                                        <div class="flex flex-col items-center gap-2 text-center">
                                            <p class="font-semibold text-sm" x-text="shopActive.name"></p>
                                            <img :src="shopActive.image" class="max-w-[72px] max-h-[72px] object-contain">
                                            <div class="flex items-center gap-1 text-sm">
                                                <img :src="currIcon(shopActive.currency_type)" class="w-4 h-4">
                                                <span class="font-semibold" x-text="totalPrice"></span>
                                            </div>
                                            <template x-if="shopActive.type !== 'b' && shopActive.type !== 'w'">
                                                <input type="number" x-model.number="buyQty" @input="calcPrice()" min="1" max="100" class="w-16 border dark:border-gray-600 dark:bg-gray-700 rounded px-2 py-1 text-sm text-center">
                                            </template>
                                            <button class="w-full border-2 border-yellow-400 bg-[#eeb425] hover:bg-[#d49f1c] text-white rounded font-semibold text-sm py-1.5 transition mt-auto disabled:opacity-50" @click="buy()" :disabled="buying">
                                                <span x-show="!buying">{{ __('Buy') }}</span>
                                                <span x-show="buying">{{ __('Buying...') }}</span>
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="!shopActive"><p class="text-gray-400 text-xs text-center mt-8">{{ __('Select an item to see details.') }}</p></template>
                                </div>
                            </div>
                        </template>

                    </div>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>
