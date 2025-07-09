<?php 

namespace Kodventure\LaravelDataExporter\Exporters;

use Kodventure\LaravelDataExporter\DTO\ExportedFileDTO;
use Kodventure\LaravelDataExporter\Contracts\ExportSourceInterface;

class JsonExporter extends BaseExporter
{
    public function export(ExportSourceInterface $exportSource): ExportedFileDTO
    {
        $rows = iterator_to_array($exportSource->getIterator());
        $contents = json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return $this->exportFile($contents);
    }
}
