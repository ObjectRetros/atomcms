<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\TranslatableResource;
use App\Rules\ValidBadgeCode;
use App\Services\Badge\BadgeImageStorage;
use App\Services\Parsers\ExternalTextsParser;
use App\Support\BadgeCode;
use Filament\Actions\Action;
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
use Illuminate\Support\Facades\Log;
use LogicException;
use RuntimeException;
use Throwable;

class BadgePage extends Page
{
    use InteractsWithForms, TranslatableResource;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Hotel';

    protected string $view = 'filament.pages.badge-page';

    protected static string $translateIdentifier = 'badge-resource';

    public bool $badgeWasPreviouslyCreated = false;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return hasHousekeepingPermission('manage_badges');
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
                            ->required()
                            ->maxLength(BadgeCode::MAX_LENGTH)
                            ->rules([new ValidBadgeCode])
                            ->afterStateUpdated(function (?string $state, Set $set) {
                                $set('code', is_string($state) ? strtoupper($state) : null);
                            })
                            ->suffixAction(fn (): PageAction => PageAction::make('search')->icon('heroicon-o-magnifying-glass')->action(fn () => $this->searchBadgesByCode()),
                            ),

                        TextInput::make('image')
                            ->label(__('filament::resources.inputs.badge_image'))
                            ->placeholder('...')
                            ->url()
                            ->maxLength(2048)
                            ->autocomplete()
                            ->visible(fn (Get $get) => isset($this->data['image']))
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
                            ->maxLength(100)
                            ->placeholder('...')
                            ->visible(fn () => isset($this->data['nitro']['title'])),

                        TextInput::make('nitro.description')
                            ->label(__('filament::resources.inputs.badge_description'))
                            ->maxLength(255)
                            ->placeholder('...')
                            ->visible(fn () => isset($this->data['nitro']['description'])),
                    ]),

                Section::make('Flash Texts')
                    ->collapsible()
                    ->visible(fn () => isset($this->data['flash']) && ! empty($this->data['flash']))
                    ->schema([
                        TextInput::make('flash.title')
                            ->label(__('filament::resources.inputs.badge_title'))
                            ->maxLength(100)
                            ->placeholder('...')
                            ->visible(fn () => isset($this->data['flash']['title'])),

                        TextInput::make('flash.description')
                            ->label(__('filament::resources.inputs.badge_description'))
                            ->maxLength(255)
                            ->placeholder('...')
                            ->visible(fn () => isset($this->data['flash']['description'])),
                    ]),
            ])
            ->statePath('data');
    }

    private function searchBadgesByCode(): void
    {
        $badgeCode = $this->badgeCode();

        if ($badgeCode === null) {
            Notification::make()
                ->danger()
                ->title(__('filament::resources.notifications.badge_code_required'))
                ->send();

            return;
        }

        $this->data['code'] = $badgeCode;

        try {
            $badgeData = app(ExternalTextsParser::class)->getBadgeData($badgeCode);
        } catch (Throwable $exception) {
            Log::channel('badge')->error('[ORION BADGE RESOURCE] - ERROR: ' . $exception->getMessage());

            Notification::make()
                ->danger()
                ->title(__('filament::resources.notifications.badge_update_failed'))
                ->send();

            return;
        }

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

    /**
     * @return array{
     *     image: string,
     *     nitro: array{title: string, description: string},
     *     flash: array{title: string, description: string}
     * }
     */
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

    public function create(): void
    {
        $form = $this->getSchema('form');

        if ($form === null) {
            throw new LogicException('The badge form schema is unavailable.');
        }

        $this->data = $form->getState();

        $nitroEnabled = config('hotel.client.nitro.enabled');
        $flashEnabled = config('hotel.client.flash.enabled');
        $badgeCode = $this->badgeCode();

        if ($badgeCode === null) {
            Notification::make()
                ->icon('heroicon-o-exclamation-triangle')
                ->iconColor('danger')
                ->color('danger')
                ->title(__('filament::resources.notifications.badge_code_required'))
                ->send();

            return;
        }

        $this->data['code'] = $badgeCode;

        // image and code fields are required when creating a new badge
        if (! $this->badgeWasPreviouslyCreated && empty($this->data['image'])) {
            Notification::make()
                ->icon('heroicon-o-exclamation-triangle')
                ->iconColor('danger')
                ->color('danger')
                ->title(__('filament::resources.notifications.badge_image_required'))
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
            $this->uploadBadgeImage($externalTextsParser, app(BadgeImageStorage::class));

            if (! empty($this->data['nitro']) && $nitroEnabled) {
                $externalTextsParser->updateNitroBadgeTexts($badgeCode, ...$this->data['nitro']);
            }
            if (! empty($this->data['flash']) && $flashEnabled) {
                $externalTextsParser->updateFlashBadgeTexts($badgeCode, ...$this->data['flash']);
            }
        } catch (Throwable $exception) {
            Log::channel('badge')->error('Badge update failed', [
                'badge_code' => $this->data['code'] ?? null,
                'exception' => $exception,
            ]);

            Notification::make()
                ->icon('heroicon-o-exclamation-triangle')
                ->iconColor('danger')
                ->color('danger')
                ->title(__('filament::resources.notifications.badge_update_failed'))
                ->send();

            return;
        }

        $this->data['image'] = $externalTextsParser->getBadgeImageUrl($badgeCode);
        $this->badgeWasPreviouslyCreated = true;

        Notification::make()
            ->icon('heroicon-o-check-circle')
            ->iconColor('success')
            ->color('success')
            ->title(__('filament::resources.notifications.badge_updated'))
            ->send();
    }

    protected function uploadBadgeImage(ExternalTextsParser $parser, BadgeImageStorage $images): void
    {
        $imageUrl = $this->data['image'] ?? null;

        if ($imageUrl === null || $imageUrl === '') {
            return;
        }

        if (! is_string($imageUrl) || filter_var($imageUrl, FILTER_VALIDATE_URL) === false) {
            throw new RuntimeException('The badge image URL is invalid.');
        }

        $badgeCode = $this->badgeCode();

        if ($badgeCode === null) {
            throw new RuntimeException('The badge code is invalid.');
        }

        if ($imageUrl === $parser->getBadgeImageUrl($badgeCode)) {
            return;
        }

        $images->storeRemote($badgeCode, $imageUrl);
    }

    private function badgeCode(): ?string
    {
        return BadgeCode::tryNormalize($this->data['code'] ?? null);
    }

    /**
     * @return array<Action|ActionGroup>
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
