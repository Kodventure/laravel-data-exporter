<?php 

namespace Kodventure\LaravelDataExporter\Contracts;

use Kodventure\LaravelDataExporter\DTO\ExportedFileDTO;
use Kodventure\LaravelDataExporter\Enums\ExportFormat;
use Kodventure\LaravelDataExporter\Enums\ExportMode;

interface SqlExporterInterface
{

    public function exportFromSql(string $sql, array $bindings, ExportFormat $format, ExportMode $mode): ExportedFileDTO;

}