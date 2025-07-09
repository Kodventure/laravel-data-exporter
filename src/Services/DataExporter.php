<?php 

namespace Kodventure\LaravelDataExporter\Services;

use Kodventure\LaravelDataExporter\Jobs\HandleExportJob;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Kodventure\LaravelDataExporter\Enums\ExportFormat;
use Kodventure\LaravelDataExporter\Enums\ExportMode;
use Kodventure\LaravelDataExporter\Contracts\ShouldBeNotifiableInterface;
use Kodventure\LaravelDataExporter\DTO\RawSqlSourceDTO;
use Kodventure\LaravelDataExporter\Factories\ExporterFactory;
use Kodventure\LaravelDataExporter\Factories\ExportSourceFactory;
use Illuminate\Support\Collection;

class DataExporter
{

    public function export(EloquentBuilder|QueryBuilder|RawSqlSourceDTO|Collection|array $source, ExportMode $mode, ExportFormat $format, ?int $page, ?int $perPage, ?array $selectedIds = [], ?ShouldBeNotifiableInterface $user = null, bool $async = true): void
    {
        $exportSource = ExportSourceFactory::make($source, $mode, $page, $perPage, $selectedIds);   
        logger("Export Source: " . get_class($exportSource));
        logger("async: " . $async);
        $exporter = ExporterFactory::make($format);    
        
        if ($async) {
            HandleExportJob::dispatch($exportSource, $exporter, $user);
        } else {
            (new HandleExportJob($exportSource, $exporter, $user))->handle();
        }
    }

}

