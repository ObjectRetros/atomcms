<x-app-layout>
    @push('title', $article->title)

    <div class="col-span-12 rounded space-y-3 md:col-span-3">
        <div
            class="relative mt-6 h-24 w-full overflow-hidden rounded border bg-white shadow-sm dark:border-gray-900 dark:bg-gray-800 md:mt-0">
            <div
                class="absolute top-1 right-1 rounded bg-white px-2 text-sm font-semibold dark:bg-gray-700 dark:text-gray-100">
                {{ $article->user && !$article->user->hidden_staff ? $article->user->permission->rank_name ?? 'Member' : 'Member' }}
            </div>

            <div class="h-[65%] w-full staff-bg"
                style="background: rgba(0, 0, 0, 0.5) url({{ asset(sprintf('assets/images/%s', $article->user ? $article->user->permission->staff_background ?? 'staff-bg.png'  : 'staff-bg.png')) }});">
            </div>

            <a href="{{ route('profile.show', $article->user ?? \App\Models\User::first()) }}" class="absolute top-4 left-1 drop-shadow">
                <img style="image-rendering: pixelated;" class="transition duration-300 ease-in-out hover:scale-105"
                    src="{{ setting('avatar_imager') }}{{ $article->user?->look }}&direction=2&head_direction=3&gesture=sml&action=wav"
                    alt="">
            </a>

            <p class="text-2xl font-semibold ml-[70px] text-white -mt-[35px]">
                {{ $article->user->username ?? setting('hotel_name') }}
            </p>

            <div class="flex w-full items-center justify-between px-4">
                <p class="ml-[57px] text-sm mt-[10px] font-semibold text-gray-500">
                    {{ $article->user->motto ?? setting('start_motto') }}
                </p>

                @if($article->user)
                    <div class="w-4 h-4 rounded-full mt-2 {{ $article->user->online ? 'bg-green-600' : 'bg-red-600' }}">
                    </div>
                @endif
            </div>
        </div>

        <x-content.content-card icon="article-icon" classes="border dark:bg-gray-800 dark:border-gray-900">
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
            class="relative flex flex-col gap-y-8 overflow-hidden rounded bg-white p-3 shadow-sm dark:bg-gray-800 dark:text-gray-300">
            <div class="relative flex h-24 flex-col items-center justify-center gap-y-1 overflow-hidden rounded px-2 text-white"
                style="background: url(/storage/{{ $article->image }}) center; background-size: cover;">
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
