<?php

namespace App\Filament\Pages;

use Illuminate\Support\Collection;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use FilipFonal\FilamentLogManager\LogViewer;
use FilipFonal\FilamentLogManager\Pages\Logs;
use Throwable;

class LogsManager extends Logs
{
    public static string $roleName = 'logs_manager';

    protected static string $view = 'filament.pages.logs-manager';

    public static function canAccess(): bool
    {
        return auth()->user()->can("view::admin::" . static::$roleName);
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament::resources.navigations.Logs');
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('logFile')
                ->searchable()
                ->reactive()
                ->hiddenLabel()
                ->placeholder(__('filament-log-manager::translations.search_placeholder'))
                ->options(fn() => $this->getFileNames($this->getFinder())->take(5))
                ->getSearchResultsUsing(fn(string $query) => $this->getFileNames($this->getFinder()->name("*{$query}*"))),
        ];
    }

    /**
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function getLogs(): Collection
    {
        if (!$this->logFile) {
            return collect([]);
        }

        $logs = null;

        try {
            $logs = LogViewer::getAllForFile($this->logFile);
        } catch (Throwable $e) {
            Notification::make()
                ->title($e->getMessage())
                ->icon('heroicon-o-exclamation-triangle')
                ->iconColor('danger')
                ->color('danger')
                ->send();
        }

        return $logs
            ? collect($logs)
            : collect([]);
    }
}
