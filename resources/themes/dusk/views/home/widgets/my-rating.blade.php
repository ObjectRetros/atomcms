<div class="flex flex-col items-center gap-2 p-2">
    <p class="text-xs font-semibold text-gray-200">
        {{ __('Average rating: :n', ['n' => $user->homeRatingStats->rating_avg ?? '0.0']) }}
    </p>

    <div class="flex gap-1">
        @for($i = 1; $i <= 5; $i++)
            <button
                type="button"
                class="transition-colors {{ auth()->check() && auth()->id() !== $user->id ? 'cursor-pointer hover:text-yellow-300' : 'cursor-default' }} {{ ($user->homeRatingStats->rating_avg ?? 0) >= $i ? 'text-yellow-400' : 'text-gray-600' }}"
                @if(auth()->check() && auth()->id() !== $user->id) data-home-rating="{{ $i }}" @endif
                aria-label="{{ __('Rate :n stars', ['n' => $i]) }}"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            </button>
        @endfor
    </div>

    <div class="flex flex-col items-center text-xs text-gray-400">
        <span>{{ __(':n votes total', ['n' => $user->homeRatingStats->total ?? 0]) }}</span>
        <span>{{ __('(:n users voted 4 or better)', ['n' => $user->homeRatingStats->most_positive ?? 0]) }}</span>
    </div>
</div>
