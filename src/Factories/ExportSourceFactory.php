<?php 

namespace Kodventure\LaravelDataExporter\Factories;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Kodventure\LaravelDataExporter\Contracts\ExportSourceInterface;
use Kodventure\LaravelDataExporter\Sources\ArrayExportSource;
use Kodventure\LaravelDataExporter\Sources\BuilderExportSource;
use Kodventure\LaravelDataExporter\Sources\SqlExportSource;
use Kodventure\LaravelDataExporter\DTO\RawSqlSourceDTO;
use Kodventure\LaravelDataExporter\Enums\ExportMode;
use InvalidArgumentException;

class ExportSourceFactory
{
    public static function make(
        mixed $source,
        ExportMode $mode = ExportMode::All,
        ?int $page = null,
        ?int $perPage = null,
        ?array $selectedIds = [],
        ?string $idKey = 'id'
    ): ExportSourceInterface {
        if ($source instanceof EloquentBuilder || $source instanceof QueryBuilder) {
            $builderExportSource = new BuilderExportSource($source, $mode, $page, $perPage, $selectedIds, $idKey);

            // Otomatik SQLExportSource'a dönüşüm
            if ($mode === ExportMode::All) {
                return $builderExportSource->toSqlExportSource();
            }

            return $builderExportSource;

        } elseif ($source instanceof RawSqlSourceDTO) {
            return new SqlExportSource($source->sql, $source->bindings, $mode, $page, $perPage, $selectedIds, $idKey);

        } elseif ($source instanceof Collection) {
            return new ArrayExportSource($source->toArray(), $mode, $page, $perPage, $selectedIds, $idKey);

        } elseif (is_array($source)) {
            return new ArrayExportSource($source, $mode, $page, $perPage, $selectedIds, $idKey);
        }

        throw new InvalidArgumentException('Unsupported export source.');
    }
}
