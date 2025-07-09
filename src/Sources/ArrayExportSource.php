<?php

namespace Kodventure\LaravelDataExporter\Sources;

use Kodventure\LaravelDataExporter\Contracts\ExportSourceInterface;
use Illuminate\Support\Arr;
use Kodventure\LaravelDataExporter\Enums\ExportMode;
use Traversable;

class ArrayExportSource implements ExportSourceInterface
{
    public function __construct(
        protected array $rows,
        protected ExportMode $mode = ExportMode::All,
        protected ?int $page = null,
        protected ?int $perPage = null,
        protected ?array $selectedIds = [],
        protected ?string $idKey = 'id',
    ) {
        logger("ArrayExportSource started...");
        logger("Export Mode: " . $this->mode->label());
    }

    public function getIterator(): Traversable
    {
        logger(count($this->rows));
        
        $filtered = match ($this->mode) {
            ExportMode::Selected => array_filter($this->rows, fn($row) => in_array(Arr::get($row, $this->idKey), $this->selectedIds)),
            ExportMode::Page => array_slice($this->rows, (($this->page - 1) * $this->perPage), $this->perPage),
            default => $this->rows,
        };

        logger(count($filtered));

        return new \ArrayIterator($filtered);
    }

    public function getHeaders(): array
    {
        $firstKey = array_key_first($this->rows);
        $firstValue = $this->rows[$firstKey];
                
        return is_array($firstValue) ? array_keys($firstValue) : [];
    }
}