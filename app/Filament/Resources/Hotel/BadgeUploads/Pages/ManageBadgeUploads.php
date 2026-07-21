<?php

namespace App\Filament\Resources\Hotel\BadgeUploads\Pages;

use App\Filament\Resources\Hotel\BadgeUploads\BadgeUploadResource;
use App\Rules\ValidBadgeUploadName;
use App\Services\Badge\BadgeImageStorage;
use App\Support\BadgeCode;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use LogicException;

class ManageBadgeUploads extends Page implements HasForms
{
    use InteractsWithForms;

    /** @var array<int, TemporaryUploadedFile|string>|TemporaryUploadedFile|string|null */
    public TemporaryUploadedFile|array|string|null $badge_file = null;

    protected static string $resource = BadgeUploadResource::class;

    protected string $view = 'filament.pages.manage-badge-uploads';

    public function mount(): void
    {
        $this->formSchema()->fill([]);
    }

    /** @return array<int, Component> */
    protected function getFormSchema(): array
    {
        return [
            FileUpload::make('badge_file')
                ->label('Upload Badge')
                ->disk('badges')
                ->acceptedFileTypes(['image/gif'])
                ->maxSize(64)
                ->rules(['mimetypes:image/gif', 'dimensions:width=40,height=40', new ValidBadgeUploadName])
                ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                    $code = strtoupper(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                    app(BadgeImageStorage::class)->store($code, (string) $file->get());

                    return BadgeCode::filename($code);
                })
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
