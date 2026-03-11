<?php 

namespace Kodventure\LaravelDataExporter\Services;

use Kodventure\LaravelDataExporter\Jobs\HandleExportJob;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Kodventure\LaravelDataExporter\Enums\ExportFormat;
use Kodventure\LaravelDataExporter\Enums\ExportMode;
use Kodventure\LaravelDataExporter\Contracts\ShouldBeNotifiableInterface;
use Kodventure\LaravelDataExporter\DTO\ExportedFileDTO;
use Kodventure\LaravelDataExporter\DTO\RawSqlSourceDTO;
use Kodventure\LaravelDataExporter\Factories\ExporterFactory;
use Kodventure\LaravelDataExporter\Factories\ExportSourceFactory;
use Kodventure\LaravelDataExporter\Notifications\ExportReadyNotification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class DataExporter
{

    public function export(EloquentBuilder|QueryBuilder|RawSqlSourceDTO|Collection|array $source, ExportMode $mode, ExportFormat $format, ?int $page, ?int $perPage, ?array $selectedIds = [], ?ShouldBeNotifiableInterface $user = null, bool $async = true): ExportedFileDTO|PendingDispatch|null
    {
        if ($mode === ExportMode::Selected && empty($selectedIds)) {
            throw new InvalidArgumentException('Selected export mode requires at least one selected ID.');
        }

        $exportSource = ExportSourceFactory::make($source, $mode, $page, $perPage, $selectedIds);   
        $exporter = ExporterFactory::make($format);    
        
        if ($async) {
            if ($user && $this->shouldNotifyStarted()) {
                $user->notify(ExportReadyNotification::started($format));
            }

            return HandleExportJob::dispatch($exportSource, $exporter, $user);

        } else {
            return (new HandleExportJob($exportSource, $exporter, $user))->handle();
        }
    }

    private function shouldNotifyStarted(): bool
    {
        return in_array($this->notificationStrategy(), ['started_only', 'started_and_completed'], true);
    }

    private function notificationStrategy(): string
    {
        $strategy = (string) Config::get('data-exporter.notifications.strategy', 'started_and_completed');
        $allowed = ['none', 'started_only', 'completed_only', 'started_and_completed'];

        return in_array($strategy, $allowed, true) ? $strategy : 'started_and_completed';
    }

}

