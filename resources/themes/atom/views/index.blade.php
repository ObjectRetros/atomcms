<x-app-layout>
    @push('title', __('Welcome to the best hotel on the web!'))

    <div class="col-span-12 space-y-14">
        <div class="col-span-12">
            <x-content.guest-content-card icon="hotel-icon">
                <x-slot:title>
                    {{ __('Latest news') }}
                </x-slot:title>

                <x-slot:under-title>
                    {{ __('Keep up to date with the latest hotel gossip.') }}
                </x-slot:under-title>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    @forelse($articles as $article)
                        <x-article-card :article="$article" />
                    @empty
                        <x-filler-article-card />
                    @endforelse
                </div>
            </x-content.guest-content-card>
        </div>

        @if($photos)
            <div class="col-span-12">
                <x-content.guest-content-card icon="camera-icon">
                    <x-slot:title>
                        {{ __('Latest Photos') }}
                    </x-slot:title>

                    <x-slot:under-title>
                        {{ __('Have a look at some of the great moments captured by users around the hotel.') }}
                    </x-slot:under-title>
                    <x-photos :photos="$photos" />
                </x-content.guest-content-card>
            </div>
        @endif
    </div>

    @push('javascript')
        <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
    @endpush

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
</x-app-layout>
