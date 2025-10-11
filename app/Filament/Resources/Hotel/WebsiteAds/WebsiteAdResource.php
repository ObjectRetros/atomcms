<?php

namespace App\Filament\Resources\Hotel\WebsiteAds;

use App\Filament\Resources\Hotel\WebsiteAds\Pages\CreateWebsiteAd;
use App\Filament\Resources\Hotel\WebsiteAds\Pages\ListWebsiteAds;
use App\Models\WebsiteAd;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class WebsiteAdResource extends Resource
{
    protected static ?string $model = WebsiteAd::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Hotel';

    protected static ?string $navigationLabel = 'ADS Images';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image')
                    ->label('Image')
                    ->disk('ads')
                    ->preserveFilenames()
                    ->image()
                    ->rules(['required', 'image', 'mimes:jpeg,png,jpg,gif'])
                    ->validationMessages([
                        'required' => 'Please upload an image.', 'image' => 'The file must be a valid image.', 'mimes' => 'Only JPEG, PNG, JPG, and GIF images are allowed.'])
                    ->required()
                    ->getUploadedFileNameForStorageUsing(
                        function (TemporaryUploadedFile $file): string {
                            return strtolower(str_replace([' ', '-', 'æ', 'ø', 'å'], ['_', '_', 'ae', 'oe', 'aa'], $file->getClientOriginalName()));
                        }
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    ImageColumn::make('image_url')
                        ->label('')
                        ->extraAttributes(['style' => 'image-rendering: pixelated'])
                        ->size(125),
                    TextColumn::make('image')
                        ->label('')
                        ->alignCenter()
                        ->searchable(),
                ]),
                TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            ->searchable();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWebsiteAds::route('/'),
            'create' => CreateWebsiteAd::route('/create'),
        ];
    }
}
