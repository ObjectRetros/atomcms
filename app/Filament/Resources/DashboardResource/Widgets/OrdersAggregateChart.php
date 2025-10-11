<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Models\User\UserOrder;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Contracts\Support\Htmlable;

class OrdersAggregateChart extends ChartWidget
{
    protected ?string $maxHeight = '300px';

    protected string $color = 'secondary';

    public function getHeading(): string|Htmlable|null
    {
        return __('filament::resources.stats.orders_chart.title');
    }

    public function getDescription(): string|Htmlable|null
    {
        return __('filament::resources.stats.orders_chart.description');
    }

    protected function getData(): array
    {
        $pendingOrder = Trend::query(UserOrder::pending())
            ->between(start: now()->startOfMonth(), end: now()->endOfMonth())
            ->perDay()
            ->count();

        $cancelledOrder = Trend::query(UserOrder::cancelled())
            ->between(start: now()->startOfMonth(), end: now()->endOfMonth())
            ->perDay()
            ->count();

        $completedOrder = Trend::query(UserOrder::completed())
            ->between(start: now()->startOfMonth(), end: now()->endOfMonth())
            ->perDay()
            ->count();

        $datasets = [
            $this->getDataset($pendingOrder, __('filament::resources.stats.orders_chart.pending'), '#fbbf24', '#f59e0b'),
            $this->getDataset($cancelledOrder, __('filament::resources.stats.orders_chart.cancelled'), '#dc2626', '#b91c1c'),
            $this->getDataset($completedOrder, __('filament::resources.stats.orders_chart.completed'), '#10b981', '#059669'),
        ];

        $data = $pendingOrder->map(fn (TrendValue $value) => $value->date)->merge(
            $cancelledOrder->map(fn (TrendValue $value) => $value->date)
        )->merge(
            $completedOrder->map(fn (TrendValue $value) => $value->date)
        )->unique()->sort()->flatten();

        return [
            'datasets' => $datasets,
            'labels' => $data,
        ];
    }

    protected function getDataset($data, $label, string $backgroundColor, string $borderColor): array
    {
        return [
            'label' => $label,
            'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
            'backgroundColor' => $backgroundColor,
            'borderColor' => $borderColor,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
