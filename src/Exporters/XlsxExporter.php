<?php 

namespace Kodventure\LaravelDataExporter\Exporters;

use Kodventure\LaravelDataExporter\DTO\ExportedFileDTO;
use Kodventure\LaravelDataExporter\Contracts\ExportSourceInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class XlsExporter extends BaseExporter
{
    public function export(ExportSourceInterface $exportSource): ExportedFileDTO
    {
        $headers = $exportSource->getHeaders();
        $rows = $exportSource->getIterator();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header yaz
        foreach ($headers as $col => $header) {
            $sheet->setCellValueByColumnAndRow($col + 1, 1, $header);
        }

        // Satırları yaz
        $rowIndex = 2;
        foreach ($rows as $row) {
            foreach ($headers as $col => $header) {
                $value = $row[$header] ?? '';
                $sheet->setCellValueByColumnAndRow($col + 1, $rowIndex, $value);
            }
            $rowIndex++;
        }

        // Geçici olarak diske kaydet
        $tempFile = tmpfile();
        $tempPath = stream_get_meta_data($tempFile)['uri'];

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $contents = file_get_contents($tempPath);
        fclose($tempFile);

        return $this->exportFile($contents);
    }
}