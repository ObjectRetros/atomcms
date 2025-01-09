<?php

namespace App\Filament\Resources\Hotel;

use App\Filament\Resources\Hotel\WebsiteAdResource\Pages;
use App\Models\WebsiteAd;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Support\Facades\Artisan;

class WebsiteAdResource extends Resource
{
    protected static ?string $model = WebsiteAd::class;

    protected static ?string $navigationGroup = 'Hotel';
    protected static ?string $navigationLabel = 'ADS Images';
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->label('Image')
                    ->disk('ads')
                    ->preserveFilenames()
                    ->image()
                    ->rules(['required', 'image', 'mimes:jpeg,png,jpg,gif'])
                    ->validationMessages([
                        'required' => 'Please upload an image.', 'image' => 'The file must be a valid image.', 'mimes' => 'Only JPEG, PNG, JPG, and GIF images are allowed.'])
                    ->required()
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
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->searchable();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWebsiteAds::route('/'),
            'create' => Pages\CreateWebsiteAd::route('/create'),
        ];
    }
}