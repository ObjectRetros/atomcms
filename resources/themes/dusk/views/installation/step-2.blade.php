<x-installation-layout>
    <x-content.installation-content-section icon="hotel-icon">
        <x-slot:title>
            {{ __('Welcome to Atom CMS') }}
        </x-slot:title>

        <x-slot:under-title>
            {{ __('We are delighted of having you trying Atom CMS') }}
        </x-slot:under-title>

        <form action="{{ route('installation.save-step') }}" method="POST" class="space-y-3">
            @csrf

            @foreach($settings as $setting)
               <div>
                   <label class="block font-semibold" for="{{ $setting->key }}">
                       {{ Str::replace('_', ' ', Str::ucfirst($setting->key)) }}
                   </label>

                   <input
                       class="focus:ring-0 border-2 border-gray-700 rounded bg-[#21242e] focus:border-[#eeb425] w-full text-gray-200 @error($setting->key)border-red-600 ring-red-500 @enderror"
                       id="{{ $setting->key }}" type="text" name="{{ $setting->key }}" value="{{ $setting->value }}" placeholder="{{ $setting->key }}" required>

                   @error($setting->key)
                   <p class="mt-1 text-xs italic text-red-500">
                       {{ $message }}
                   </p>
                   @enderror

                   <small>{{ $setting->comment }}</small>
               </div>
            @endforeach

            <x-form.secondary-button>
                {{ __('Continue to step 3') }}
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
