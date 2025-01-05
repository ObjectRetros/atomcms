<?php

namespace App\Filament\Resources\Hotel\BadgeUploadResource\Pages;

use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Notifications\Notification; // Import the Notification class
use Illuminate\Support\Facades\Storage;

class ManageBadgeUploads extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public $badge_file;

    protected static string $resource = 'App\Filament\Resources\Hotel\BadgeUploadResource';
    protected static string $view = 'filament.pages.manage-badge-uploads';

    public function mount(): void
    {
        $this->form->fill([]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\FileUpload::make('badge_file')
                ->label('Upload Badge')
                ->disk('badges')
                ->preserveFilenames()
                ->acceptedFileTypes(['image/gif'])
                ->rules(['mimes:gif'])
                ->required(),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Notification::make()
            ->title('Badge uploaded successfully!')
            ->success()
            ->send();
    }
}
