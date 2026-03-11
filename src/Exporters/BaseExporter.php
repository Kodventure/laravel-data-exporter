<?php 

namespace Kodventure\LaravelDataExporter\Exporters;

use Carbon\Carbon;
use Illuminate\Support\Str; 
use Kodventure\LaravelDataExporter\Contracts\ExportSourceInterface;
use Kodventure\LaravelDataExporter\DTO\ExportedFileDTO;
use Illuminate\Support\Facades\Config;
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
        return 'data_'.Carbon::now()->format('Y-m-d_H-i-s').$this->format->extension();
    }

    protected function exportFile(string $contents): ExportedFileDTO
    {
        $filename = $this->getFilename();
        $disk = (string) Config::get('data-exporter.storage.disk', 'public');
        $path = (string) Config::get('data-exporter.storage.path', 'exports');
        $fullPath = $path . '/' . $filename;
    
        Storage::disk($disk)->put($fullPath, $contents);

        $url = null;
        $temporaryUrl = null;
        $size = null;

        try {
            $url = Storage::disk($disk)->url($fullPath);
        } catch (\Throwable) {
            $url = null;
        }

        try {
            $ttl = (int) Config::get('data-exporter.storage.ttl_minutes', 30);
            $temporaryUrl = Storage::disk($disk)->temporaryUrl($fullPath, now()->addMinutes($ttl));
        } catch (\Throwable) {
            $temporaryUrl = null;
        }

        try {
            $size = Storage::disk($disk)->size($fullPath);
        } catch (\Throwable) {
            $size = null;
        }
    
        return new ExportedFileDTO(
            name: $filename,
            path: $fullPath,
            format: $this->format,
            url: $url,
            temporaryUrl: $temporaryUrl,
            size: $size,
        );
    }    
}