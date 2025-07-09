<?php

namespace Kodventure\LaravelDataExporter\Contracts;

use Kodventure\LaravelDataExporter\Enums\ExportFormat;
use Kodventure\LaravelDataExporter\DTO\ExportedFileDTO;
use Kodventure\LaravelDataExporter\Contracts\ExportSourceInterface;

interface ExporterInterface
{
    public function export(ExportSourceInterface $source): ExportedFileDTO;
    public function getFormat(): ExportFormat;
    public function setFormat(ExportFormat $format): self;
}