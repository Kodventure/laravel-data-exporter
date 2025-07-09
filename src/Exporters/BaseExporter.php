<?php 

namespace Kodventure\LaravelDataExporter\Exporters;

use Illuminate\Support\Str; 
use Kodventure\LaravelDataExporter\Contracts\ExportSourceInterface;
use Kodventure\LaravelDataExporter\DTO\ExportedFileDTO;
use Illuminate\Support\Facades\Storage;
use Kodventure\LaravelDataExporter\Contracts\ExporterInterface;
use Kodventure\LaravelDataExporter\Enums\ExportFormat;

// Bu class source alir ve diske vs export eder, kim veriyor ona bakmaz. 
// source'dan getIterator() ile veri alir ve export eder.
// Zaten bunu kullanan class belli bir formata göre çıktı veriyor.

abstract class BaseExporter implements ExporterInterface
{
    public ExportFormat $format;    

    public function __construct(ExportFormat $format)
    {
        $this->format = $format;
    }    

    public function getFormat(): ExportFormat
    {
        return $this->format;
    }

    public function setFormat(ExportFormat $format): self
    {
        $this->format = $format;
        return $this;
    }

    protected function getFilename(): string
    {
        return 'data_'.now()->format('Y-m-d_H-i-s').$this->format->extension();
    }

    protected function exportFile(string $contents): ExportedFileDTO
    {
        $filename = $this->getFilename();
        $disk = config('data-exporter.storage.disk', 'public');
        $path = config('data-exporter.storage.path', 'exports');
        $fullPath = $path . '/' . $filename;
    
        Storage::disk($disk)->put($fullPath, $contents);
    
        return new ExportedFileDTO(
            name: $filename,
            path: $fullPath,
            format: $this->format, // eğer sınıfın içinde tutuyorsan
            // url: Storage::disk($disk)->url($fullPath)
        );
    }    
}