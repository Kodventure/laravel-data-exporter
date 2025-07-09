<?php 

namespace Kodventure\LaravelDataExporter\Exporters;

use Kodventure\LaravelDataExporter\DTO\ExportedFileDTO;
use Kodventure\LaravelDataExporter\Contracts\ExportSourceInterface;

class CsvExporter extends BaseExporter
{
    public function export(ExportSourceInterface $exportSource): ExportedFileDTO
    {
        $headers = $exportSource->getHeaders();
        $rows = $exportSource->getIterator();

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $headers);

        foreach ($rows as $row) {
            $data = $row instanceof \Illuminate\Contracts\Support\Arrayable ? $row->toArray() : (array) $row;

            $orderedRow = array_map(function ($header) use ($data) {
                $value = $data[$header] ?? '';
                return is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
            }, $headers);
            fputcsv($handle, $orderedRow);
        }

        rewind($handle);
        $contents = stream_get_contents($handle);
        fclose($handle);

        return $this->exportFile($contents);
    }
}