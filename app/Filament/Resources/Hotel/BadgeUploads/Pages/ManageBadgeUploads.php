<?php

namespace App\Filament\Resources\Hotel\BadgeUploads\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page; // Import the Notification class

class ManageBadgeUploads extends Page implements HasForms
{
    use InteractsWithForms;

    public $badge_file;

    protected static string $resource = 'App\Filament\Resources\Hotel\BadgeUploads\BadgeUploadResource';

    protected string $view = 'filament.pages.manage-badge-uploads';

    public function mount(): void
    {
        $this->form->fill([]);
    }

    protected function getFormSchema(): array
    {
        return [
            FileUpload::make('badge_file')
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
