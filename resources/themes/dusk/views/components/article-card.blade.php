@props(['article'])

<div class="swiper-slide relative article-image rounded-lg overflow-hidden" style="background-image: url({{ asset('storage/' . $article->image) }})">
    <div class="absolute h-[90px] w-full left-0 bottom-0 bg-[#171a23]/95 text-white py-2 px-4">
        <h2 class="text-xl font-bold truncate">
            {{ $article->title }}
        </h2>

        <div class="flex justify-between items-center mt-1">
            <div class="py-1 px-2 rounded-md bg-black/60 text-sm mt-2 flex gap-1 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>

                {{ $article->user->username }}
            </div>

            <a href="{{ route('article.show', $article->slug) }}" class="text-sm mt-1 read-more-link hover:underline">
                Read more
            </a>

        </div>
    </div>
</div>
