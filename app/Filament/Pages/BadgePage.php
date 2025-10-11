<?php

namespace App\Filament\Pages;

use App\Filament\Traits\TranslatableResource;
use App\Services\Parsers\ExternalTextsParser;
use Filament\Actions\Action as PageAction;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class BadgePage extends Page
{
    use InteractsWithForms, TranslatableResource;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Hotel';

    protected string $view = 'filament.pages.badge-page';

    protected static string $translateIdentifier = 'badge-resource';

    public $badgeWasPreviouslyCreated;

    public ?array $data = [];

    public static string $roleName = 'badge_page';

    public static function canAccess(): bool
    {
        return auth()->user()->can('view::admin::' . static::$roleName);
    }

    public function getTitle(): string|Htmlable
    {
        return __(
            sprintf('filament::resources.resources.%s.navigation_label', static::$translateIdentifier),
        );
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament::resources.tabs.Main'))
                    ->schema([
                        TextInput::make('code')
                            ->label(__('filament::resources.inputs.badge_code'))
                            ->helperText(__('filament::resources.helpers.badge_code_helper'))
                            ->afterStateUpdated(function (?string $state, Set $set) {
                                $set('code', strtoupper($state));
                            })
                            ->suffixAction(fn (): PageAction => PageAction::make('search')->icon('heroicon-o-magnifying-glass')->action(fn () => $this->searchBadgesByCode()),
                            ),

                        TextInput::make('image')
                            ->label(__('filament::resources.inputs.badge_image'))
                            ->placeholder('...')
                            ->autocomplete()
                            ->visible(fn (Get $get) => isset($this->data['image']) ?? false)
                            ->prefixAction(
                                fn (?string $state): PageAction => PageAction::make('visit')
                                    ->icon('heroicon-s-arrow-top-right-on-square')
                                    ->tooltip(__('filament::resources.common.Open link'))
                                    ->url($state)
                                    ->visible(fn () => ! empty($state))
                                    ->openUrlInNewTab(),
                            ),
                    ]),

                Section::make('Nitro Texts')
                    ->collapsible()
                    ->visible(fn () => isset($this->data['nitro']) && ! empty($this->data['nitro']))
                    ->schema([
                        TextInput::make('nitro.title')
                            ->label(__('filament::resources.inputs.badge_title'))
                            ->placeholder('...')
                            ->visible(fn () => isset($this->data['nitro']['title']) ?? false),

                        TextInput::make('nitro.description')
                            ->label(__('filament::resources.inputs.badge_description'))
                            ->placeholder('...')
                            ->visible(fn () => isset($this->data['nitro']['description']) ?? false),
                    ]),

                Section::make('Flash Texts')
                    ->collapsible()
                    ->visible(fn () => isset($this->data['flash']) && ! empty($this->data['flash']))
                    ->schema([
                        TextInput::make('flash.title')
                            ->label(__('filament::resources.inputs.badge_title'))
                            ->placeholder('...')
                            ->visible(fn () => isset($this->data['flash']['title']) ?? false),

                        TextInput::make('flash.description')
                            ->label(__('filament::resources.inputs.badge_description'))
                            ->placeholder('...')
                            ->visible(fn () => isset($this->data['flash']['description']) ?? false),
                    ]),
            ])
            ->statePath('data');
    }

    private function searchBadgesByCode(): void
    {
        $badgeCode = $this->form->getState()['code'] ?? null;

        if (empty($badgeCode)) {
            $this->notify('danger', __('filament::resources.notifications.badge_code_required'));

            return;
        }

        $badgeData = app(ExternalTextsParser::class)->getBadgeData($badgeCode);
        $this->badgeWasPreviouslyCreated = is_array($badgeData['nitro']) || is_array($badgeData['flash']);

        if ($this->badgeWasPreviouslyCreated) {
            Notification::make()
                ->icon('heroicon-o-check-circle')
                ->iconColor('success')
                ->color('success')
                ->title(__('filament::resources.notifications.badge_found'))
                ->send();

            $this->data = [
                'code' => $badgeCode,
                ...$this->getDefaultDataBehavior(
                    $badgeData['image'] ?? null,
                    $badgeData['nitro']['title'] ?? null,
                    $badgeData['nitro']['description'] ?? null,
                    $badgeData['flash']['title'] ?? null,
                    $badgeData['flash']['description'] ?? null,
                ),
            ];

            return;
        }

        Notification::make()
            ->color('success')
            ->icon('heroicon-o-check-circle')
            ->iconColor('success')
            ->title(__('filament::resources.notifications.create_badge'))
            ->send();

        $this->data = [
            'code' => $badgeCode,
            ...$this->getDefaultDataBehavior(),
        ];
    }

    private function getDefaultDataBehavior(
        ?string $badgeImageUrl = null,
        ?string $nitroTitle = null,
        ?string $nitroDesc = null,
        ?string $flashTitle = null,
        ?string $flashDesc = null,
    ): array {
        return [
            'image' => $badgeImageUrl ?? '',
            'nitro' => [
                'title' => $nitroTitle ?? '',
                'description' => $nitroDesc ?? '',
            ],
            'flash' => [
                'title' => $flashTitle ?? '',
                'description' => $flashDesc ?? '',
            ],
        ];
    }

    public function create()
    {
        $nitroEnabled = config('hotel.client.nitro.enabled');
        $flashEnabled = config('hotel.client.flash.enabled');

        // image and code fields are required when creating a new badge
        if (! $this->badgeWasPreviouslyCreated && (empty($this->data['image']) || empty($this->data['code']))) {
            $notificationTitle = empty($this->data['image']) ?
                __('filament::resources.notifications.badge_image_required') :
                __('filament::resources.notifications.badge_code_required');

            Notification::make()
                ->icon('heroicon-o-exclamation-triangle')
                ->iconColor('danger')
                ->color('danger')
                ->title($notificationTitle)
                ->send();

            return;
        }

        $externalTextsParser = app(ExternalTextsParser::class);

        if ((empty($this->data['nitro']) && $nitroEnabled) || (empty($this->data['flash']) && $flashEnabled)) {
            Notification::make()
                ->icon('heroicon-o-exclamation-triangle')
                ->iconColor('danger')
                ->color('danger')
                ->title(__('filament::resources.notifications.badge_texts_required'))
                ->send();

            return;
        }

        try {
            $this->uploadBadgeImage($externalTextsParser);

            if (! empty($this->data['nitro']) && $nitroEnabled) {
                $externalTextsParser->updateNitroBadgeTexts($this->data['code'], ...$this->data['nitro']);
            }
            if (! empty($this->data['flash']) && $flashEnabled) {
                $externalTextsParser->updateFlashBadgeTexts($this->data['code'], ...$this->data['flash']);
            }
        } catch (Throwable $exception) {
            Log::channel('badge')->error('[ORION BADGE RESOURCE] - ERROR: ' . $exception->getMessage());

            Notification::make()
                ->icon('heroicon-o-exclamation-triangle')
                ->iconColor('danger')
                ->color('danger')
                ->title(__('filament::resources.notifications.badge_update_failed'))
                ->send();

            return;
        }

        $this->data['image'] = $externalTextsParser->getBadgeImageUrl($this->data['code']);
        $this->badgeWasPreviouslyCreated = true;

        Notification::make()
            ->icon('heroicon-o-check-circle')
            ->iconColor('success')
            ->color('success')
            ->title(__('filament::resources.notifications.badge_updated'))
            ->send();
    }

    protected function uploadBadgeImage(ExternalTextsParser $parser): void
    {
        if (empty($this->data['image']) || ! filter_var($this->data['image'], FILTER_VALIDATE_URL)) {
            return;
        }

        if ($this->data['image'] == $parser->getBadgeImageUrl($this->data['code'])) {
            return;
        }

        $image = Http::get($this->data['image']);

        if (! $image->successful()) {
            return;
        }

        $contentType = $image->header('content-type');

        $gdImage = match ($contentType) {
            'image/png' => imagecreatefrompng($this->data['image']),
            'image/gif' => imagecreatefromgif($this->data['image']),
            'image/jpeg' => imagecreatefromjpeg($this->data['image']),
            default => false
        };

        if ($gdImage === false) {
            Notification::make()
                ->icon('heroicon-o-exclamation-triangle')
                ->iconColor('danger')
                ->color('danger')
                ->title(__('filament::resources.notifications.badge_image_upload_failed'))
                ->send();

            return;
        }

        $uploadPath = public_path(sprintf('%s%s%s.gif',
            rtrim(config('hotel.client.flash.relative_files_path'), '\//'),
            '/c_images/album1584/',
            $this->data['code'],
        ));

        imagegif($gdImage, $uploadPath);
    }

    /**
     * @return array<\Filament\Actions\Action|ActionGroup>
     */
    protected function getHeaderActions(): array
    {
        return [
            PageAction::make('save')
                ->label(__('filament::resources.common.Update'))
                ->action(fn () => $this->create())
                ->color('primary')
                ->visible(fn () => isset($this->data['code']) && $this->badgeWasPreviouslyCreated),

            PageAction::make('create')
                ->label(__('filament::resources.common.Create'))
                ->action(fn () => $this->create())
                ->color('success')
                ->visible(fn () => isset($this->data['code']) && ! $this->badgeWasPreviouslyCreated),
        ];
    }
}
