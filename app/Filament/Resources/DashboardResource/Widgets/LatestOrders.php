<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\User\UserOrder;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Filament\Resources\Shop\ShopOrderResource;

class LatestOrders extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(UserOrder::latest())
            ->paginated([3, 5, 8])
            ->columns(ShopOrderResource::getTable())
            ->actions([
                Tables\Actions\ViewAction::make()->form(ShopOrderResource::getForm()),
            ]);
    }
}
