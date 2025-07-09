<?php

namespace Kodventure\LaravelDataExporter\Sources;

use Kodventure\LaravelDataExporter\Enums\ExportMode;
use Kodventure\LaravelDataExporter\Contracts\ExportSourceInterface;
use Illuminate\Support\Facades\DB;
use Traversable;

class SqlExportSource implements ExportSourceInterface
{
    public function __construct(
        protected string $sql,
        protected array $bindings = [],
        protected ExportMode $mode = ExportMode::All,
        protected ?int $page = null,
        protected ?int $perPage = null,
        protected ?array $selectedIds = [],
        protected ?string $idKey = 'id',
    ) {
        logger("SqlExportSource started...");
        logger("Export Mode: " . $this->mode->label());
    }


    public function getIterator(): Traversable
    {
        return match ($this->mode) {
            ExportMode::Selected => !empty($this->selectedIds)
                ? DB::select(
                    "SELECT * FROM ({$this->sql}) AS sub WHERE {$this->idKey} IN (" . implode(',', array_fill(0, count($this->selectedIds), '?')) . ")",
                    [...$this->bindings, ...$this->selectedIds]
                )
                : new \ArrayIterator([]),

            ExportMode::Page => DB::select(
                $this->sql . " LIMIT {$this->perPage} OFFSET " . (($this->page - 1) * $this->perPage),
                $this->bindings
            ),

            ExportMode::All => DB::cursor($this->sql, $this->bindings),
        };        
    }


    public function getHeaders(): array
    {
        $first = DB::selectOne($this->sql, $this->bindings);
        return $first ? array_keys((array) $first) : [];
    }
}