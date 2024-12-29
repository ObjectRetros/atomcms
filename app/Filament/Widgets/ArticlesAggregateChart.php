<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;

class ArticlesAggregateChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '300px';

    protected static string $color = 'primary';

    public function getHeading(): string|Htmlable|null
    {
        return __('filament::resources.stats.articles_chart.title');
    }

    public function getDescription(): string|Htmlable|null
    {
        return __('filament::resources.stats.articles_chart.description');
    }

    protected function getData(): array
    {
        $data = Trend::model(Article::class)
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfMonth()
            )
            ->perDay()
            ->count();

        $label = __('filament::resources.stats.articles_chart.label');

        return [
            'datasets' => [
                [
                    'label' => $label,
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
