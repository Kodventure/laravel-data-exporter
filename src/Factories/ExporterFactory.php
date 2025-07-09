<?php 

namespace Kodventure\LaravelDataExporter\Factories;

use Kodventure\LaravelDataExporter\Enums\ExportFormat;
use Kodventure\LaravelDataExporter\Exporters\CsvExporter;
use Kodventure\LaravelDataExporter\Exporters\JsonExporter;
use Kodventure\LaravelDataExporter\Exporters\SqlExporter;
use Kodventure\LaravelDataExporter\Exporters\XlsxExporter;
use Kodventure\LaravelDataExporter\Exporters\BaseExporter;
use InvalidArgumentException;

class ExporterFactory
{
    public static function make(ExportFormat $format): BaseExporter
    {
        return match ($format) {
            ExportFormat::CSV => app(CsvExporter::class, ['format' => $format]),
            ExportFormat::XLSX => app(XlsxExporter::class, ['format' => $format]), 
            ExportFormat::JSON => app(JsonExporter::class, ['format' => $format]), 
            ExportFormat::SQL => app(SqlExporter::class, ['format' => $format]), 
            default => throw new InvalidArgumentException(__('Unsupported export format: :format', ['format' => $format->value])),
        };
    }
}
