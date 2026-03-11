<?php

namespace Kodventure\LaravelDataExporter\Exporters;

use Kodventure\LaravelDataExporter\DTO\ExportedFileDTO;
use Kodventure\LaravelDataExporter\Contracts\ExportSourceInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class XlsxExporter extends BaseExporter
{
    public function export(ExportSourceInterface $exportSource): ExportedFileDTO
    {
        $headers = $exportSource->getHeaders();
        $rows = $exportSource->getIterator();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        foreach ($headers as $col => $header) {
            $sheet->setCellValue([$col + 1, 1], $header);
        }

        $rowIndex = 2;
        foreach ($rows as $row) {
            foreach ($headers as $col => $header) {
                $value = $this->normalizeCellValue($row[$header] ?? '');
                $sheet->setCellValue([$col + 1, $rowIndex], $value);
            }
            $rowIndex++;
        }

        $tempFile = tmpfile();
        $tempPath = stream_get_meta_data($tempFile)['uri'];

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $contents = file_get_contents($tempPath);
        fclose($tempFile);

        return $this->exportFile($contents);
    }

    private function normalizeCellValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        if ($value instanceof \Stringable) {
            return (string) $value;
        }

        if (is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        return $value;
    }
}
