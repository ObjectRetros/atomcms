<x-app-layout>
    @push('title', __('Photos'))

    <div class="col-span-12">
        <x-content.content-card icon="camera-icon">
            <x-slot:title>
                {{ __('Latest Photos') }}
            </x-slot:title>

            <x-slot:under-title>
                {{ __('Have a look at some of the great moments captured by users around the hotel.') }}
            </x-slot:under-title>

            <x-photos :photos="$photos" />
        </x-content.content-card>

        {{ $photos->links() }}
    </div>

    @push('javascript')
        <script src="{{ asset('assets/vendor/js/fancybox.umd.js') }}"></script>
    @endpush

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/fancybox.css') }}" />
</x-app-layout>
