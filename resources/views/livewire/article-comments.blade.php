@php($theme = setting('theme', 'dusk'))

<div class="space-y-4">
    @if ($article->can_comment)
        @if (auth()->check() && !$article->userHasReachedArticleCommentLimit())
            <x-content.content-card icon="hotel-icon">
                <x-slot:title>
                    {{ __('Post a comment') }}
                </x-slot:title>

                <x-slot:under-title>
                    {{ __('Post a comment on the article, to let us know what you think about it') }}
                </x-slot:under-title>

                <div class="text-sm {{ $theme === 'dusk' ? 'text-gray-100' : 'text-gray-700 dark:text-gray-200' }}">
                    <form wire:submit="postComment">
                        <textarea wire:model="comment"
                            wire:keydown.ctrl.enter.prevent="postComment"
                            maxlength="255"
                            class="focus:ring-0 border-2 rounded w-full min-h-[110px] max-h-[160px] @error('comment') border-red-600 ring-red-500 @enderror {{ $theme === 'dusk' ? 'border-gray-600 bg-gray-800 text-gray-100 focus:border-[#eeb425]' : 'border-gray-300 bg-white text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 focus:border-[#eeb425]' }}"
                            placeholder="{{ __('Write a comment...') }}"></textarea>

                        @error('comment')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror

                        <div class="mt-2 flex items-center justify-between text-xs {{ $theme === 'dusk' ? 'text-gray-300' : 'text-gray-500 dark:text-gray-400' }}">
                            <span>{{ __('Tip: Press Ctrl + Enter to post quickly') }}</span>
                            <span>{{ mb_strlen($comment) }}/255</span>
                        </div>

                        <x-form.primary-button classes="mt-2" wire:target="postComment" wire:loading.attr="disabled">
                            {{ __('Post comment') }}
                        </x-form.primary-button>
                    </form>
                </div>
            </x-content.content-card>
        @endif

        @if(count($article->comments))
            <x-content.content-card icon="hotel-icon">
                <x-slot:title>
                    {{ __('Comments') }}
                </x-slot:title>

                <x-slot:under-title>
                    {{ __('Below you will see all the comments, written on this article') }}
                </x-slot:under-title>

                <div class="space-y-[13px] {{ $theme === 'dusk' ? 'text-gray-100' : 'text-gray-700 dark:text-gray-200' }}">
                    @foreach ($article->comments->sortByDesc('created_at') as $comment)
                        <div
                            wire:key="article-comment-{{ $comment->id }}"
                            class="w-full rounded-xl p-3 shadow-sm transition {{ $theme === 'dusk' ? 'bg-[#21242e] border border-[#2b3040]' : 'bg-[#f5f5f5] dark:bg-gray-700 border border-gray-200 dark:border-gray-600' }}">
                            <div class="flex items-start gap-3">
                                <a href="{{ route('profile.show', $comment->user) }}" class="shrink-0 drop-shadow-sm">
                                    <span class="flex items-center justify-center rounded-lg border p-0 {{ $theme === 'dusk' ? 'border-gray-600 bg-[#2c3140]' : 'border-gray-300 bg-white dark:border-gray-600 dark:bg-gray-800' }}">
                                        <img style="image-rendering: pixelated;"
                                             class="transition duration-300 ease-in-out hover:scale-105"
                                             src="{{ setting('avatar_imager') }}{{ $comment->user->look }}&headonly=1&direction=2&head_direction=3&gesture=sml"
                                             alt="{{ $comment->user->username }}">
                                    </span>
                                </a>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <a href="{{ route('profile.show', $comment->user) }}"
                                           class="truncate font-semibold text-[#89cdf0] hover:underline">
                                            {{ $comment->user->username }}
                                        </a>

                                        <div class="flex items-center gap-2 shrink-0">
                                            <p class="text-xs {{ $theme === 'dusk' ? 'text-gray-300' : 'text-gray-500 dark:text-gray-300' }}">
                                                {{ $comment->created_at->diffForHumans() }}
                                            </p>

                                            @if ($comment->canBeDeleted())
                                                <button wire:click="deleteComment({{ $comment->id }})"
                                                    class="rounded p-1 {{ $theme === 'dusk' ? 'text-gray-300 hover:text-red-400 hover:bg-red-500/10' : 'text-gray-500 dark:text-gray-300 hover:text-red-500 hover:bg-red-500/10' }} transition"
                                                    title="{{ __('Delete comment') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                         class="h-4 w-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    <p class="mt-1 text-sm leading-relaxed break-words {{ $theme === 'dusk' ? 'text-gray-100' : 'text-gray-700 dark:text-gray-100' }}">
                                        {{ $comment->comment }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-content.content-card>
        @endif
    @endif
</div>
