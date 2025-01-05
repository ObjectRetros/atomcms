<x-filament::page>
    <div class="space-y-4">
        <h2 class="text-lg font-bold">Upload a New Badge</h2>

        {{-- Render the form --}}
        <form wire:submit.prevent="save" class="space-y-4">
            {{ $this->form }}

            <x-filament::button type="submit">
                Upload Badge
            </x-filament::button>
        </form>
    </div>
</x-filament::page>
