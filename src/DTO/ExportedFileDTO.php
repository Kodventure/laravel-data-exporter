<?php

namespace Kodventure\LaravelDataExporter\DTO;

use Illuminate\Contracts\Support\Arrayable;
use Kodventure\LaravelDataExporter\Enums\ExportFormat;

class ExportedFileDTO implements Arrayable
{
    public function __construct(
        public string $name,
        public string $path,
        public ExportFormat $format,
        public ?string $url = null,
        public ?string $temporaryUrl = null,
        public ?int $size = null,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'path' => $this->path,
            'format' => $this->format,
            'url' => $this->url,
            'temporaryUrl' => $this->temporaryUrl,
            'size' => $this->size,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            path: $data['path'],
            format: $data['format'],
            url: $data['url'] ?? null,
            temporaryUrl: $data['temporaryUrl'] ?? null,
            size: $data['size'] ?? null
        );
    }
}