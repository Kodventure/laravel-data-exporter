<?php

namespace Kodventure\LaravelDataExporter\Sources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Kodventure\LaravelDataExporter\Enums\ExportMode;
use Kodventure\LaravelDataExporter\Contracts\ExportSourceInterface;
use Traversable;

/* QueryBuilder veya EloquentBuilder */
// Bu SQLExportSource döndürmeli ki, serialize edilebilsin..
class BuilderExportSource implements ExportSourceInterface
{
    public function __construct(
        protected Builder|QueryBuilder $query,
        protected ExportMode $mode = ExportMode::All,
        protected ?int $page = null,
        protected ?int $perPage = null,
        protected ?array $selectedIds = [],
        protected ?string $idKey = 'id',
    ) {
        logger("BuilderExportSource started...");
        logger("Export Mode: " . $this->mode->label());
    }

    public function getIterator(): Traversable
    {
        $q = clone $this->query; 

        return match ($this->mode) {
            ExportMode::Selected => $q->whereIn($this->idKey, $this->selectedIds)->get(),
            ExportMode::Page => $q->forPage($this->page, $this->perPage)->get(),
            ExportMode::All => $q->cursor(),
        };        
    }    

    public function _getIterator(): Traversable
    {
        $q = clone $this->query; 

        return match ($this->mode) {
            ExportMode::Selected => new \ArrayIterator($q->whereIn($this->idKey, $this->selectedIds)->get()),
            ExportMode::Page => new \ArrayIterator($q->forPage($this->page, $this->perPage)->get()),
            ExportMode::All => $q->cursor(),
        };        
    }

    public function getHeaders(): array
    {
        $first = (clone $this->query)->first();
        return $first ? array_keys($first->getAttributes()) : [];
    }

    // BuilderExportSource'dan SqlExportSource'a dönüşü sağlar
    // BuilderExportSource job'a gönderilemiyor cünkü.
    public function toSqlExportSource(): SqlExportSource
    {
        return new SqlExportSource(
            $this->query->toSql(),
            $this->query->getBindings(),
            $this->mode,
            $this->page,
            $this->perPage,
            $this->selectedIds,
            $this->idKey
        );
    }    
}