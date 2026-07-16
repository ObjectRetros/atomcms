<?php

namespace App\Filament\Resources\Hotel\BadgeUploads\Pages;

use App\Filament\Resources\Hotel\BadgeUploads\BadgeUploadResource;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use LogicException;

class ManageBadgeUploads extends Page implements HasForms
{
    use InteractsWithForms;

    public $badge_file;

    protected static string $resource = BadgeUploadResource::class;

    protected string $view = 'filament.pages.manage-badge-uploads';

    public function mount(): void
    {
        $this->formSchema()->fill([]);
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
        $this->formSchema()->getState();

        Notification::make()
            ->title('Badge uploaded successfully!')
            ->success()
            ->send();
    }

    private function formSchema(): Schema
    {
        return $this->getSchema('form')
            ?? throw new LogicException('The badge upload form schema is not registered.');
    }
}
