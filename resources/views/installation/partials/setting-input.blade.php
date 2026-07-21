<div>
    <label class="block font-semibold text-gray-700" for="{{ $setting->key }}">
        {{ Str::replace('_', ' ', Str::ucfirst($setting->key)) }}
    </label>

    <input
        class="focus:ring-0 border-4 border-gray-200 rounded focus:border-[#eeb425] w-full @error($setting->key)border-red-600 ring-red-500 @enderror"
        id="{{ $setting->key }}"
        type="text"
        name="{{ $setting->key }}"
        value="{{ $setting->value }}"
        placeholder="{{ $setting->key }}"
        @required($setting->isRequiredDuringInstallation())>

    @error($setting->key)
        <p class="mt-1 text-xs italic text-red-500">
            {{ $message }}
        </p>
    @enderror

    <small>{{ $setting->comment }}</small>
</div>
