<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Filament\Resources\Shop\ShopOrderResource;
use App\Models\User\UserOrder;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(UserOrder::latest())
            ->paginated([3, 5, 8])
            ->columns(ShopOrderResource::getTable())
            ->recordActions([
                ViewAction::make()->schema(ShopOrderResource::getForm()),
            ]);
    }
}
