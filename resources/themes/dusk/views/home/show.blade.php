<x-app-layout>
    @push('title', __('Home of :u', ['u' => $user->username]))

    <div class="col-span-12 flex flex-col items-center gap-4" x-data="homeManager('{{ $user->username }}', {{ $isMe ? 'true' : 'false' }})">

        @if($isMe)
            <div class="w-full max-w-[928px]">
                <template x-if="editing">
                    <div class="flex justify-between items-center">
                        <div class="flex gap-2">
                            <button class="border-2 border-blue-400 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm px-4 py-1.5 rounded transition" @click="openBag('inventory')" x-show="!saving">{{ __('Inventory') }}</button>
                            <button class="border-2 border-[#d49f1c] bg-[#eeb425] hover:bg-[#d49f1c] text-white font-semibold text-sm px-4 py-1.5 rounded transition" @click="openBag('shop')" x-show="!saving">{{ __('Shop') }}</button>
                        </div>
                        <div class="flex gap-2">
                            <button class="border-2 border-red-400 bg-red-600 hover:bg-red-500 text-white font-semibold text-sm px-4 py-1.5 rounded transition" @click="cancel()" x-show="!saving">{{ __('Cancel') }}</button>
                            <button class="border-2 border-green-500 bg-green-600 hover:bg-green-700 text-white font-semibold text-sm px-4 py-1.5 rounded transition disabled:opacity-50" @click="save()" :disabled="saving">
                                <span x-show="!saving">{{ __('Save') }}</span>
                                <span x-show="saving">{{ __('Saving...') }}</span>
                            </button>
                        </div>
                    </div>
                </template>
                <template x-if="!editing">
                    <button class="border-2 border-[#d49f1c] bg-[#eeb425] hover:bg-[#d49f1c] text-white font-semibold text-sm px-5 py-1.5 rounded transition" @click="editing = true">{{ __('Edit Home') }}</button>
                </template>
            </div>
        @else
            <h2 class="text-xl font-semibold text-gray-100">{{ __('Home of :u', ['u' => $user->username]) }}</h2>
        @endif

        <template x-if="previewing">
            <div class="w-full max-w-[928px] flex items-center justify-between bg-cyan-900/50 border border-cyan-700 rounded-lg px-4 py-2">
                <span class="text-sm text-cyan-200">{{ __('Preview mode — drag items to arrange, then purchase') }}</span>
                <div class="flex gap-2">
                    <button class="border border-gray-500 text-gray-300 hover:bg-gray-700 text-sm font-semibold px-4 py-1 rounded transition" @click="endPreview()">{{ __('Cancel') }}</button>
                    <button class="border-2 border-green-500 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-1 rounded transition" @click="openConfirmModal()">{{ __('Buy & Save') }}</button>
                </div>
            </div>
        </template>

        <div
            x-ref="canvas"
            class="home-canvas relative w-full max-w-[928px] h-[1360px] border rounded-lg overflow-hidden bg-cover bg-center bg-no-repeat"
            :class="previewing ? 'border-cyan-700' : 'border-gray-700'"
            :style="{ backgroundImage: `url(${bg()})` }"
        >
            <template x-for="item in visible()" :key="item.id">
                <div
                    class="absolute select-none"
                    :class="{
                        'cursor-grab active:cursor-grabbing': editing && !item._preview,
                        'ring-2 ring-[#eeb425] rounded': selectedItem?.id === item.id,
                        'opacity-60 ring-2 ring-dashed ring-cyan-400 rounded cursor-grab active:cursor-grabbing': item._preview,
                    }"
                    :style="{ left: (item.x||0)+'px', top: (item.y||0)+'px', zIndex: item.z||0, transform: item.is_reversed ? 'scaleX(-1)' : '' }"
                    :data-home-item="item.id"
                    @click="select(item)"
                >
                    <template x-if="item.home_item?.type === 's'">
                        <img :src="img(item.home_item?.image)" class="max-w-none pointer-events-none" draggable="false">
                    </template>
                    <template x-if="item.home_item?.type === 'n'">
                        <div class="p-3 min-w-[150px] min-h-[80px] bg-amber-50 border border-amber-200 rounded shadow text-xs text-gray-800 pointer-events-none" x-text="item.parsed_data || item.extra_data || ''"></div>
                    </template>
                    <template x-if="item.home_item?.type === 'w'">
                        <div class="min-w-[270px] max-w-[300px] bg-[#2b303c] border border-gray-600 rounded-lg shadow overflow-hidden pointer-events-none">
                            <div class="bg-[#21242e] px-3 py-2 font-semibold text-sm text-gray-100" x-text="item.home_item.name"></div>
                            <div class="p-2 text-sm text-gray-200" x-html="item.content || ''"></div>
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
        <div
            x-show="showBag"
            style="display: none"
            x-on:keydown.escape.prevent.stop="showBag = false"
            role="dialog"
            aria-modal="true"
            x-id="['bag-modal']"
            :aria-labelledby="$id('bag-modal')"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div x-show="showBag" x-transition.opacity class="fixed inset-0 bg-black/60"></div>
            <div x-show="showBag" x-transition x-on:click="showBag = false" class="relative flex min-h-screen items-center justify-center p-4">
                <div x-on:click.stop x-trap.noscroll.inert="showBag" class="relative bg-[#2b303c] rounded-lg shadow-2xl w-full max-w-[820px] flex flex-col text-gray-100 overflow-hidden" style="max-height: 75vh">

                    <div class="flex items-center bg-[#21242e] px-3 py-2 gap-1 shrink-0" :id="$id('bag-modal')">
                        <button class="px-4 py-1.5 rounded text-sm font-semibold transition" :class="bagTab === 'inventory' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700'" @click="bagTab = 'inventory'; fetchInv()">{{ __('Inventory') }}</button>
                        <button class="px-4 py-1.5 rounded text-sm font-semibold transition" :class="bagTab === 'shop' ? 'bg-[#eeb425] text-white' : 'text-gray-400 hover:text-white hover:bg-gray-700'" @click="bagTab = 'shop'; fetchShopCats()">{{ __('Shop') }}</button>
                        <button class="ml-auto text-gray-500 hover:text-white text-lg leading-none px-2" @click="showBag = false">&times;</button>
                    </div>

                    <div class="flex flex-1 min-h-0">

                        <template x-if="bagTab === 'inventory'">
                            <div class="flex w-full min-h-0">
                                <div class="w-44 shrink-0 border-r border-gray-700 p-2 flex flex-col gap-0.5">
                                    <template x-for="t in ['stickers','notes','widgets','backgrounds']" :key="t">
                                        <button class="text-left px-3 py-1.5 rounded text-sm capitalize transition" :class="invTab === t ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700'" @click="invTab = t; invActive = null; invSelected = []; placeQty = 1" x-text="t"></button>
                                    </template>
                                </div>
                                <div class="flex-1 flex flex-col min-h-0">
                                    <div class="flex items-center justify-between px-3 pt-2 pb-1 shrink-0" x-show="invItems().length > 0">
                                        <button class="text-[11px] text-gray-400 hover:text-white transition" @click="invSelectAll()" x-text="invSelected.length === invItems().length ? '{{ __('Deselect all') }}' : '{{ __('Select all') }}'"></button>
                                        <span class="text-[11px] text-gray-500" x-show="invSelected.length > 1" x-text="invSelected.length + ' selected'"></span>
                                    </div>
                                    <div class="flex-1 px-3 pb-3 overflow-y-auto">
                                        <template x-if="invLoading"><p class="text-gray-500 text-sm text-center py-10">{{ __('Loading...') }}</p></template>
                                        <template x-if="!invLoading">
                                            <div class="flex flex-wrap gap-1.5">
                                                <template x-for="item in invItems()" :key="item.home_item_id">
                                                    <div class="w-16 h-16 border rounded flex items-center justify-center cursor-pointer relative transition"
                                                        :class="invIsSelected(item) ? 'border-[#eeb425] bg-[#eeb425]/10' : 'border-gray-600 hover:border-gray-400'"
                                                        @click="invToggle(item)" @dblclick="quickPlace(item)">
                                                        <img :src="img(item.home_item?.image)" class="max-w-[56px] max-h-[56px] object-contain" :style="invTab === 'backgrounds' ? 'image-rendering: pixelated' : ''">
                                                        <span x-show="item.item_ids?.length > 1" class="absolute -top-1 -right-1 bg-blue-600 text-white text-[9px] rounded-full min-w-[16px] h-4 flex items-center justify-center px-0.5" x-text="item.item_ids?.length"></span>
                                                        <div x-show="invIsSelected(item)" class="absolute top-0 left-0 w-4 h-4 bg-[#eeb425] rounded-br flex items-center justify-center">
                                                            <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                        </div>
                                                    </div>
                                                </template>
                                                <template x-if="invItems().length === 0"><p class="text-gray-500 text-sm w-full text-center py-10">{{ __('No items here.') }}</p></template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div class="w-44 shrink-0 border-l border-gray-700 p-3 flex flex-col">
                                    <template x-if="invSelected.length > 1">
                                        <div class="flex flex-col items-center gap-2 text-center">
                                            <p class="font-semibold text-sm"><span x-text="invSelected.length"></span> {{ __('items selected') }}</p>
                                            <button class="w-full border-2 border-green-500 bg-green-600 hover:bg-green-700 text-white rounded font-semibold text-sm py-1.5 transition" @click="place()">{{ __('Place All') }}</button>
                                        </div>
                                    </template>
                                    <template x-if="invSelected.length === 1 && invActive">
                                        <div class="flex flex-col items-center gap-2 text-center">
                                            <p class="font-semibold text-sm" x-text="invActive.home_item?.name"></p>
                                            <img :src="img(invActive.home_item?.image)" class="max-w-[72px] max-h-[72px] object-contain">
                                            <p class="text-xs text-gray-400"><span x-text="invActive.item_ids?.length"></span> {{ __('available') }}</p>
                                            <template x-if="invActive.home_item?.type === 's' && invActive.item_ids?.length > 1">
                                                <input type="number" x-model.number="placeQty" min="1" :max="Math.min(15, invActive.item_ids?.length)" class="w-16 bg-gray-700 border border-gray-600 rounded px-2 py-1 text-sm text-white text-center">
                                            </template>
                                            <button class="w-full border-2 border-green-500 bg-green-600 hover:bg-green-700 text-white rounded font-semibold text-sm py-1.5 transition mt-auto" @click="place()">{{ __('Place') }}</button>
                                        </div>
                                    </template>
                                    <template x-if="invSelected.length === 0"><p class="text-gray-500 text-xs text-center mt-8">{{ __('Click items to select, double-click to quick-place.') }}</p></template>
                                </div>
                            </div>
                        </template>

                        <template x-if="bagTab === 'shop'">
                            <div class="flex w-full min-h-0">
                                <div class="w-44 shrink-0 border-r border-gray-700 p-2 flex flex-col gap-0.5 overflow-y-auto">
                                    <button class="text-left px-3 py-1.5 rounded text-sm transition" :class="shopTab === 'notes' ? 'bg-[#eeb425] text-white' : 'text-gray-300 hover:bg-gray-700'" @click="setShopTab('notes')">{{ __('Notes') }}</button>
                                    <button class="text-left px-3 py-1.5 rounded text-sm transition" :class="shopTab === 'widgets' ? 'bg-[#eeb425] text-white' : 'text-gray-300 hover:bg-gray-700'" @click="setShopTab('widgets')">{{ __('Widgets') }}</button>
                                    <button class="text-left px-3 py-1.5 rounded text-sm transition" :class="shopTab === 'backgrounds' ? 'bg-[#eeb425] text-white' : 'text-gray-300 hover:bg-gray-700'" @click="setShopTab('backgrounds')">{{ __('Backgrounds') }}</button>
                                    <div class="border-t border-gray-700 mt-1 pt-1">
                                        <p class="text-[10px] text-gray-500 uppercase tracking-wider px-3 mb-1">{{ __('Stickers') }}</p>
                                        <template x-for="cat in shopCategories" :key="cat.id">
                                            <button class="text-left px-3 py-1 rounded text-sm w-full flex items-center gap-1.5 transition truncate" :class="shopTab === 'cat-'+cat.id ? 'bg-[#eeb425] text-white' : 'text-gray-300 hover:bg-gray-700'" @click="openCat(cat.id)">
                                                <img :src="img(cat.icon)" class="w-4 h-4 shrink-0 object-contain">
                                                <span x-text="cat.name" class="truncate"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                <div class="flex-1 flex flex-col min-h-0">
                                    <div class="flex items-center justify-between px-3 pt-2 pb-1 shrink-0" x-show="shopItems.length > 0 && shopTab !== 'home'">
                                        <button class="text-[11px] text-gray-400 hover:text-white transition" @click="shopSelectAll()" x-text="shopSelected.length === shopItems.length ? '{{ __('Deselect all') }}' : '{{ __('Select all') }}'"></button>
                                        <span class="text-[11px] text-gray-500" x-show="shopSelected.length > 1" x-text="shopSelected.length + ' selected'"></span>
                                    </div>
                                    <div class="flex-1 px-3 pb-3 overflow-y-auto">
                                        <template x-if="shopTab === 'home'"><p class="text-gray-500 text-sm text-center py-10">{{ __('Pick a category to browse items.') }}</p></template>
                                        <template x-if="shopTab !== 'home'">
                                            <div>
                                                <template x-if="shopLoading"><p class="text-gray-500 text-sm text-center py-10">{{ __('Loading...') }}</p></template>
                                                <template x-if="!shopLoading">
                                                    <div class="flex flex-wrap gap-1.5">
                                                        <template x-for="item in shopItems" :key="item.id">
                                                            <div class="w-16 h-16 border rounded flex items-center justify-center cursor-pointer relative transition"
                                                                :class="shopIsSelected(item) ? 'border-[#eeb425] bg-[#eeb425]/10' : 'border-gray-600 hover:border-gray-400'"
                                                                @click="shopToggle(item)">
                                                                <img :src="img(item.image)" class="max-w-[56px] max-h-[56px] object-contain" :style="item.type === 'b' ? 'image-rendering: pixelated' : ''">
                                                                <span class="absolute bottom-0 right-0 bg-black/70 text-[9px] text-gray-300 px-1 rounded-tl leading-tight" x-text="item.price"></span>
                                                                <div x-show="shopIsSelected(item)" class="absolute top-0 left-0 w-4 h-4 bg-[#eeb425] rounded-br flex items-center justify-center">
                                                                    <svg class="w-2.5 h-2.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                                </div>
                                                            </div>
                                                        </template>
                                                        <template x-if="shopItems.length === 0"><p class="text-gray-500 text-sm w-full text-center py-10">{{ __('No items.') }}</p></template>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div class="w-48 shrink-0 border-l border-gray-700 p-3 flex flex-col">
                                    <div class="mb-3 pb-2 border-b border-gray-700" x-show="userBalance">
                                        <p class="text-[10px] text-gray-500 uppercase tracking-wider mb-1.5">{{ __('Your balance') }}</p>
                                        <div class="grid grid-cols-2 gap-x-2 gap-y-1">
                                            <template x-for="[key, label] in [['-1', 'Credits'], ['0', 'Duckets'], ['5', 'Diamonds'], ['101', 'Points']]" :key="key">
                                                <div class="flex items-center gap-1">
                                                    <img :src="currIcon(key)" class="w-3.5 h-3.5">
                                                    <span class="text-[11px] font-medium text-gray-200" x-text="getBalance(key).toLocaleString()"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <template x-if="shopSelected.length > 1">
                                        <div class="flex flex-col items-center gap-2 text-center">
                                            <p class="font-semibold text-sm"><span x-text="shopSelected.length"></span> {{ __('items selected') }}</p>
                                            <div class="w-full space-y-0.5">
                                                <template x-for="group in totalsByCurrency()" :key="group.currency_type">
                                                    <div class="flex items-center justify-between text-xs">
                                                        <div class="flex items-center gap-1">
                                                            <img :src="currIcon(group.currency_type)" class="w-3.5 h-3.5">
                                                            <span x-text="currName(group.currency_type)"></span>
                                                        </div>
                                                        <span class="font-semibold" :class="getBalance(group.currency_type) < group.total ? 'text-red-500' : ''" x-text="group.total"></span>
                                                    </div>
                                                </template>
                                            </div>
                                            <button class="w-full border-2 border-[#d49f1c] bg-[#eeb425] hover:bg-[#d49f1c] text-white rounded font-semibold text-sm py-1.5 transition disabled:opacity-50 disabled:cursor-not-allowed" @click="buy()" :disabled="buying || !canAffordSelection()">
                                                <span x-show="!buying && canAffordSelection()">{{ __('Buy All') }}</span>
                                                <span x-show="!buying && !canAffordSelection()">{{ __('Insufficient funds') }}</span>
                                                <span x-show="buying">{{ __('Buying...') }}</span>
                                            </button>
                                            <button class="w-full border-2 border-green-500 bg-green-600 hover:bg-green-700 text-white rounded font-semibold text-sm py-1.5 transition disabled:opacity-50 disabled:cursor-not-allowed" @click="buyAndPlace()" :disabled="buying || !canAffordSelection()">
                                                {{ __('Buy & Place All') }}
                                            </button>
                                            <button class="w-full border border-cyan-600 text-cyan-300 hover:bg-cyan-900/40 rounded text-sm py-1 transition mt-1" @click="previewSelected()">
                                                {{ __('Preview') }}
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="shopSelected.length === 1 && shopActive">
                                        <div class="flex flex-col items-center gap-2 text-center">
                                            <p class="font-semibold text-sm" x-text="shopActive.name"></p>
                                            <img :src="img(shopActive.image)" class="max-w-[72px] max-h-[72px] object-contain">
                                            <div class="flex items-center gap-1 text-sm">
                                                <img :src="currIcon(shopActive.currency_type)" class="w-4 h-4">
                                                <span class="font-semibold" x-text="shopActive.price * buyQty"></span>
                                            </div>
                                            <p class="text-[11px]" :class="canAffordItem(shopActive, buyQty) ? 'text-green-400' : 'text-red-500 font-semibold'" x-text="canAffordItem(shopActive, buyQty) ? `${currName(shopActive.currency_type)}: ${getBalance(shopActive.currency_type).toLocaleString()} available` : `Not enough ${currName(shopActive.currency_type)} (have ${getBalance(shopActive.currency_type).toLocaleString()})`"></p>
                                            <template x-if="shopActive.type !== 'b' && shopActive.type !== 'w'">
                                                <input type="number" x-model.number="buyQty" @input="clampBuyQty()" min="1" max="100" class="w-16 bg-gray-700 border border-gray-600 rounded px-2 py-1 text-sm text-white text-center">
                                            </template>
                                            <button class="w-full border-2 border-[#d49f1c] bg-[#eeb425] hover:bg-[#d49f1c] text-white rounded font-semibold text-sm py-1.5 transition disabled:opacity-50 disabled:cursor-not-allowed" @click="buy()" :disabled="buying || !canAffordItem(shopActive, buyQty)">
                                                <span x-show="!buying && canAffordItem(shopActive, buyQty)">{{ __('Buy') }}</span>
                                                <span x-show="!buying && !canAffordItem(shopActive, buyQty)">{{ __('Insufficient funds') }}</span>
                                                <span x-show="buying">{{ __('Buying...') }}</span>
                                            </button>
                                            <button class="w-full border-2 border-green-500 bg-green-600 hover:bg-green-700 text-white rounded font-semibold text-sm py-1.5 transition disabled:opacity-50 disabled:cursor-not-allowed" @click="buyAndPlace()" :disabled="buying || !canAffordItem(shopActive, buyQty)">
                                                {{ __('Buy & Place') }}
                                            </button>
                                            <button class="w-full border border-cyan-600 text-cyan-300 hover:bg-cyan-900/40 rounded text-sm py-1 transition mt-1" @click="preview(shopActive)">
                                                {{ __('Preview') }}
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="shopSelected.length === 0"><p class="text-gray-500 text-xs text-center mt-8">{{ __('Click items to select, or select all.') }}</p></template>
                                </div>
                            </div>
                        </template>

                    </div>
                </div>
            </div>
        </div>

        {{-- Confirm Purchase Modal --}}
        <div
            x-show="showConfirmModal"
            style="display: none"
            x-on:keydown.escape.prevent.stop="showConfirmModal = false"
            role="dialog"
            aria-modal="true"
            x-id="['confirm-modal']"
            :aria-labelledby="$id('confirm-modal')"
            class="fixed inset-0 z-50 overflow-y-auto"
        >
            <div x-show="showConfirmModal" x-transition x-on:click="showConfirmModal = false" class="relative flex min-h-screen items-center justify-center overflow-hidden p-4">
                <div x-show="showConfirmModal" x-transition.opacity class="fixed inset-0 bg-black/50"></div>

                <div x-on:click.stop x-trap.noscroll.inert="showConfirmModal" class="relative w-full max-w-md rounded px-6 py-6 shadow-md bg-[#21242e] text-gray-200">
                    <button type="button" x-on:click="showConfirmModal = false" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex items-center dark:hover:bg-gray-800 dark:hover:text-white">
                        <svg aria-hidden="true" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        <span class="sr-only">{{ __('Close modal') }}</span>
                    </button>

                    <div class="my-4 flex flex-col items-center" :id="$id('confirm-modal')">
                        <h3 class="font-semibold text-lg text-gray-100" x-text="`{{ __('Purchase') }} ${confirmItems.length} {{ __('item(s)') }}`"></h3>
                    </div>

                    <div class="max-h-[200px] overflow-y-auto space-y-1">
                        <template x-for="item in confirmItems" :key="item.id">
                            <div class="flex items-center justify-between gap-2 py-1.5 border-b border-gray-700">
                                <div class="flex items-center gap-2 min-w-0">
                                    <img :src="img(item.image)" class="w-8 h-8 object-contain shrink-0">
                                    <span class="truncate text-sm" x-text="item.name"></span>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <span class="font-semibold text-sm" x-text="item.price"></span>
                                    <img :src="currIcon(item.currency_type)" class="w-4 h-4">
                                    <button x-show="confirmItems.length > 1" type="button" class="ml-1 text-red-500 hover:text-red-400 text-lg leading-none" @click="confirmRemoveItem(item.id)">&times;</button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="border-t border-gray-700 pt-3 mt-3 space-y-1">
                        <template x-for="[curr, cost] in Object.entries(confirmCosts)" :key="curr">
                            <div class="flex justify-between text-xs" :class="getBalance(curr) < cost ? 'text-red-500 font-bold' : 'text-gray-400'">
                                <span x-text="currName(curr)"></span>
                                <span x-text="`${cost} / ${getBalance(curr)} ${getBalance(curr) < cost ? '(insufficient)' : ''}`"></span>
                            </div>
                        </template>
                    </div>

                    <template x-if="confirmUnaffordable.length > 0">
                        <p class="text-red-500 text-xs mt-2" x-text="`{{ __('Not enough') }} ${confirmUnaffordable.join(', ')}. {{ __('Remove items to proceed.') }}`"></p>
                    </template>

                    <div class="mt-5 flex gap-2">
                        <button type="button" @click="showConfirmModal = false" class="w-full rounded bg-red-500 hover:bg-red-600 text-white p-2 border-2 border-red-400 transition ease-in-out duration-150 font-semibold">{{ __('Cancel') }}</button>
                        <x-form.secondary-button type="button" x-on:click="confirmPurchase()" x-bind:disabled="confirmUnaffordable.length > 0" classes="disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="confirmUnaffordable.length > 0">{{ __('Cannot afford') }}</span>
                            <span x-show="confirmUnaffordable.length === 0">{{ __('Confirm Purchase') }}</span>
                        </x-form.secondary-button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Toast --}}
        <div
            x-show="toast"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed bottom-6 right-6 z-[60] px-4 py-2.5 rounded-lg shadow-lg text-sm font-semibold text-white transition-all"
            :class="toast?.type === 'error' ? 'bg-red-500' : 'bg-green-600'"
            x-text="toast?.msg"
        ></div>
    </div>
</x-app-layout>
