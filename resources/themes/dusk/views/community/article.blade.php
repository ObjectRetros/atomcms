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
        </div>

        <livewire:article-reactions :article="$article" :key="'reactions-' . $article->id" />

        <livewire:article-comments :article="$article" :key="$article->id" />
    </div>
</x-app-layout>
