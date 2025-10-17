<x-modals.modal-wrapper>
    <div class="select-none"
        x-data='reactions(@json($myReactions), @json($articleReactions), "{{ route('article.toggle-reaction', $article->slug) }}")'>
        <div class="mt-4 flex w-full flex-wrap gap-2 rounded-lg p-2 bg-gray-900">
            <div x-show="isAuthenticated"
                class="px-2 hover:scale-110 transition-all font-semibold h-8 flex items-center justify-center border-2 text-xs border-yellow-400 cursor-pointer bg-[#eeb425] text-white rounded-lg"
                x-on:click="open = true">
                {{ __('Add') }}
            </div>

            <template x-for="articleReaction in articleReactions">
                <div>
                    <div class="flex h-8 w-12 items-center justify-center gap-2 rounded-lg border-2 text-sm font-bold border-gray-800 hover:bg-gray-700 cursor-pointer"
                        :class="{
                            bg-gray-800 border-gray-700': userHasReaction(
                                articleReaction),
                            'cursor-pointer hover:scale-110 transition-all hover:bg-gray-700': isAuthenticated
                        }"
                        @click="toggleReaction(articleReaction.name)" :data-popover-target="articleReaction.id">
                        <img :src="'/assets/images/icons/reactions/' + articleReaction.name + '.png'"
                            :alt="articleReaction.name">
                        <span x-text="articleReaction.count"></span>
                    </div>

                    <div data-popover :id="articleReaction.id" role="tooltip"
                        class="invisible absolute z-10 inline-block w-64 rounded-lg border border-gray-200 bg-white text-sm font-light text-gray-500 opacity-0 shadow-xs transition-opacity duration-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400">
                        <div
                            class="rounded-t-lg border-b border-gray-200 bg-gray-100 px-3 py-2 dark:border-gray-600 dark:bg-gray-700">
                            <div
                                class="flex w-full items-center justify-center font-semibold text-gray-900 dark:text-white">
                                {{ __('Reactions with') }} <img
                                    :src="'/assets/images/icons/reactions/' + articleReaction.name + '.png'"
                                    class="ml-1" :alt="articleReaction.name">
                            </div>
                        </div>
                        <div class="overflow-y-auto px-3 py-2" style="max-height: 200px">
                            <template x-for="user in articleReaction.users">
                                <p class="w-full text-center" x-text="user"></p>
                            </template>
                        </div>
                        <div data-popper-arrow></div>
                    </div>
                </div>
            </template>
        </div>

        <div x-show="isAuthenticated">
            <x-modals.regular-modal>
                <x-slot name="title">
                    <h2 class="text-2xl">
                        {{ __('Insert Reaction') }}
                    </h2>
                </x-slot>

                <div class="flex w-full flex-wrap justify-center gap-3 p-2">
                    <template x-for="defaultReaction in allReactions">
                        <div class="cursor-pointer rounded-lg border-2 px-3 py-2 hover:bg-gray-700 hover:border-g border-gray-800"
                            x-show="canAddReactionFromModal(defaultReaction)" @click="toggleReaction(defaultReaction)">
                            <img :src="'/assets/images/icons/reactions/' + defaultReaction + '.png'"
                                :alt="defaultReaction">
                        </div>
                    </template>
                </div>
            </x-modals.regular-modal>
        </div>
    </div>
</x-modals.modal-wrapper>

@push('scripts')
    <script>
        window.App = {
            defaultReactions: @json(config('habbo.reactions')),
            isAuthenticated: @json(auth()->check())
        }
    </script>
@endpush
