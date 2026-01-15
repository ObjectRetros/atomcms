<x-app-layout>
    @push('title', $article->title)

    <div class="col-span-12 rounded space-y-3 md:col-span-3">
        <x-community.staff-card :user="$article->user" />

        <x-content.content-card icon="article-icon">
            <x-slot:title>
                {{ __('Other articles') }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('Our most recent articles') }}
            </x-slot:under-title>

            <div class="flex flex-col gap-y-2">
                @forelse($otherArticles as $art)
                    <a href="{{ route('article.show', $art->slug) }}"
                        style="background: rgba(0, 0, 0, 0.5) url({{ $art->image }}) center;"
                        class="w-full rounded h-12 bg-blue-200 transition ease-in-out duration-200 hover:scale-[103%] text-white flex justify-center items-center font-bold recent-articles">
                        {{ Str::limit($art->title, 20) }}
                    </a>
                @empty
                    <p class="dark:text-gray-400">
                        {{ __('There is currently no other articles') }}
                    </p>
                @endforelse
            </div>
        </x-content.content-card>
    </div>

    <div class="col-span-12 space-y-4 md:col-span-9">
        <div
            class="relative flex flex-col gap-y-8 overflow-hidden rounded p-3 shadow-sm bg-gray-800 text-gray-100">
            <div class="relative flex h-24 flex-col items-center justify-center gap-y-1 overflow-hidden rounded px-2 text-white"
                style="background: url({{ asset('storage/' .  $article->image ) }}) center; background-size: cover;">
                <div class="absolute h-full w-full bg-black/50"></div>

                <p class="relative w-full truncate text-center text-xl font-semibold lg:text-2xl xl:text-3xl">
                    {{ $article->title }}</p>
                <p class="relative w-full truncate text-center">{{ $article->short_story }}</p>
            </div>

            <div class="px-2" id="article-content">
                {!! $article->full_story !!}
            </div>

            @include('community.partials.article-reactions')
        </div>

        @if ($article->can_comment)
            @if (auth()->check() && !$article->userHasReachedArticleCommentLimit())
                <x-content.content-card icon="hotel-icon">
                    <x-slot:title>
                        {{ __('Post a comment') }}
                    </x-slot:title>

                    <x-slot:under-title>
                        {{ __('Post a comment on the article, to let us know what you think about it') }}
                    </x-slot:under-title>

                    <div class="text-sm dark:text-gray-200">
                        <form action="{{ route('article.comment.store', $article) }}" method="POST">
                            @csrf

                            <textarea name="comment"
                                class="focus:ring-0 rounded focus:border-[#eeb425] w-full min-h-[100px] max-h-[100px] bg-gray-800 @error('comment') border-red-600 ring-red-500 @enderror"></textarea>

                            <x-form.primary-button classes="mt-2">
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

                    <div class="px-1 dark:text-gray-200 space-y-[13px]">
                        @foreach ($article->comments->sortByDesc('created_at') as $comment)
                            <div
                                class="relative w-full rounded bg-[#21242e] p-4 h-[90px] overflow-hidden flex items-center shadow-sm">
                                <a href="{{ route('profile.show', $comment->user) }}"
                                   class="absolute top-2 left-1 drop-shadow">
                                    <img style="image-rendering: pixelated;"
                                         class="transition duration-300 ease-in-out hover:scale-105"
                                         src="{{ setting('avatar_imager') }}{{ $comment->user->look }}&direction=2&head_direction=3&gesture=sml&action=wav"
                                         alt="">
                                </a>

                                <div class="flex justify-between ml-[60px] w-full">
                                    <div class="text-sm">
                                        <a href="{{ route('profile.show', $comment->user) }}"
                                           class="font-semibold text-[#89cdf0] dark:text-blue-300 hover:underline">
                                            {{ $comment->user->username }}
                                        </a>

                                        <p class="block dark:text-gray-200">
                                            {{ $comment->comment }}
                                        </p>
                                    </div>

                                    <div class="flex gap-x-2">
                                        <p class="text-xs dark:text-gray-200">
                                            {{ $comment->created_at->diffForHumans() }}
                                        </p>

                                        @if ($comment->canBeDeleted())
                                            <form action="{{ route('article.comment.destroy', $comment) }}" method="POST"
                                                  class="cursor-pointer transition duration-200 ease-in-out hover:scale-105">
                                                @method('DELETE')
                                                @csrf

                                                <button type="submit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                         class="h-4 w-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-content.content-card>
            @endif
        @endif
    </div>
</x-app-layout>
