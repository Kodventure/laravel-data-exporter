<?php 

namespace Kodventure\LaravelDataExporter\Contracts;

interface ExportSourceInterface extends \IteratorAggregate
{
    public function getHeaders(): array;
}