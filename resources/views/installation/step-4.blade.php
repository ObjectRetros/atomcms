<x-installation-layout>
    <x-content.installation-content-section icon="hotel-icon" classes="border">
        <x-slot:title>
            {{ __('Welcome to Atom CMS') }}
        </x-slot:title>

        <x-slot:under-title>
            {{ __('We are delighted of having you trying Atom CMS') }}
        </x-slot:under-title>

        <form action="{{ route('installation.save-step') }}" method="POST" class="space-y-3">
            @csrf

            @foreach($settings as $setting)
                @include('installation.partials.setting-input')
            @endforeach

            <x-form.secondary-button>
                {{ __('Complete setup') }}
            </x-form.secondary-button>
        </form>

        <div class="flex gap-x-4">
            <form action="{{ route('installation.previous-step') }}" method="POST" class="w-full mt-3">
                @csrf

                <x-form.primary-button>
                    {{ __('Previous step') }}
                </x-form.primary-button>
            </form>

            <form action="{{ route('installation.restart') }}" method="POST" class="w-full mt-3">
                @csrf

                <x-form.danger-button>
                    {{ __('Restart installation') }}
                </x-form.danger-button>
            </form>
        </div>
    </x-content.installation-content-section>
</x-installation-layout>
