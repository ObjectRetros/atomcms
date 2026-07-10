<x-app-layout>
    @push('title', __('Photos'))

    <div class="col-span-12 space-y-6">
        <x-page-header>
            <x-slot:icon>
                <img src="{{ asset('/assets/images/dusk/camera_icon.png') }}" alt="">
            </x-slot:icon>

            {{ __('Photos') }}
        </x-page-header>

        <div class="col-span-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($photos as $photo)
               <x-photo :photo="$photo" />
            @endforeach
        </div>
    </div>
</x-app-layout>
