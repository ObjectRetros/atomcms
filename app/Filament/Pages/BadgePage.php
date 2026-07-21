<?php

namespace App\Filament\Pages;

use App\Filament\Traits\TranslatableResource;
use App\Services\Parsers\ExternalTextsParser;
use App\Support\BadgeCode;
use App\Support\OutboundHttp;
use App\Support\PublicHttpUrlResolver;
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
use Throwable;

class BadgePage extends Page
{
    private const MAX_BADGE_IMAGE_BYTES = 1_048_576;

    use InteractsWithForms, TranslatableResource;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Hotel';

    protected string $view = 'filament.pages.badge-page';

    protected static string $translateIdentifier = 'badge-resource';

    public bool $badgeWasPreviouslyCreated = false;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public static string $roleName = 'badge_page';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('view::admin::' . static::$roleName) ?? false;
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
                            ->regex('/\A[A-Z0-9_-]+\z/')
                            ->afterStateUpdated(function (?string $state, Set $set) {
                                $set('code', is_string($state) ? strtoupper($state) : null);
                            })
                            ->suffixAction(fn (): PageAction => PageAction::make('search')->icon('heroicon-o-magnifying-glass')->action(fn () => $this->searchBadgesByCode()),
                            ),

                        TextInput::make('image')
                            ->label(__('filament::resources.inputs.badge_image'))
                            ->placeholder('...')
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
                            ->placeholder('...')
                            ->visible(fn () => isset($this->data['nitro']['title'])),

                        TextInput::make('nitro.description')
                            ->label(__('filament::resources.inputs.badge_description'))
                            ->placeholder('...')
                            ->visible(fn () => isset($this->data['nitro']['description'])),
                    ]),

                Section::make('Flash Texts')
                    ->collapsible()
                    ->visible(fn () => isset($this->data['flash']) && ! empty($this->data['flash']))
                    ->schema([
                        TextInput::make('flash.title')
                            ->label(__('filament::resources.inputs.badge_title'))
                            ->placeholder('...')
                            ->visible(fn () => isset($this->data['flash']['title'])),

                        TextInput::make('flash.description')
                            ->label(__('filament::resources.inputs.badge_description'))
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
            if (! $this->uploadBadgeImage($externalTextsParser, $badgeCode)) {
                return;
            }

            if (! empty($this->data['nitro']) && $nitroEnabled) {
                $externalTextsParser->updateNitroBadgeTexts($badgeCode, ...$this->data['nitro']);
            }
            if (! empty($this->data['flash']) && $flashEnabled) {
                $externalTextsParser->updateFlashBadgeTexts($badgeCode, ...$this->data['flash']);
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

        $this->data['image'] = $externalTextsParser->getBadgeImageUrl($badgeCode);
        $this->badgeWasPreviouslyCreated = true;

        Notification::make()
            ->icon('heroicon-o-check-circle')
            ->iconColor('success')
            ->color('success')
            ->title(__('filament::resources.notifications.badge_updated'))
            ->send();
    }

    protected function uploadBadgeImage(ExternalTextsParser $parser, string $badgeCode): bool
    {
        $imageUrl = $this->data['image'] ?? null;

        if (! is_string($imageUrl) || trim($imageUrl) === '') {
            return true;
        }

        if ($imageUrl === $parser->getBadgeImageUrl($badgeCode)) {
            return true;
        }

        $resolvedUrl = app(PublicHttpUrlResolver::class)->resolve($imageUrl);

        if ($resolvedUrl === null) {
            $this->notifyBadgeImageUploadFailed();

            return false;
        }

        $image = OutboundHttp::request()
            ->withOptions([
                'allow_redirects' => false,
                'curl' => [
                    CURLOPT_RESOLVE => [$resolvedUrl->curlResolveEntry()],
                    CURLOPT_PROXY => '',
                    CURLOPT_NOPROXY => '*',
                    CURLOPT_NOPROGRESS => false,
                    CURLOPT_XFERINFOFUNCTION => static fn (
                        mixed $handle,
                        float $downloadSize,
                        float $downloaded,
                        float $uploadSize,
                        float $uploaded,
                    ): int => $downloaded > self::MAX_BADGE_IMAGE_BYTES ? 1 : 0,
                ],
            ])
            ->get($resolvedUrl->url);

        if (! $image->successful() || strlen($image->body()) > self::MAX_BADGE_IMAGE_BYTES) {
            $this->notifyBadgeImageUploadFailed();

            return false;
        }

        $contentType = strtolower(trim(explode(';', $image->header('content-type'), 2)[0]));

        try {
            $gdImage = in_array($contentType, ['image/png', 'image/gif', 'image/jpeg'], true)
                ? imagecreatefromstring($image->body())
                : false;
        } catch (Throwable) {
            $gdImage = false;
        }

        if ($gdImage === false) {
            $this->notifyBadgeImageUploadFailed();

            return false;
        }

        $uploadPath = public_path(sprintf('%s%s%s.gif',
            rtrim(config('hotel.client.flash.relative_files_path'), '\//'),
            '/c_images/album1584/',
            $badgeCode,
        ));

        try {
            $saved = imagegif($gdImage, $uploadPath);
        } finally {
            imagedestroy($gdImage);
        }

        if (! $saved) {
            $this->notifyBadgeImageUploadFailed();
        }

        return $saved;
    }

    private function badgeCode(): ?string
    {
        return BadgeCode::normalize($this->data['code'] ?? null);
    }

    private function notifyBadgeImageUploadFailed(): void
    {
        Notification::make()
            ->icon('heroicon-o-exclamation-triangle')
            ->iconColor('danger')
            ->color('danger')
            ->title(__('filament::resources.notifications.badge_image_upload_failed'))
            ->send();
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
