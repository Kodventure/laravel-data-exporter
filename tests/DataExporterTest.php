<?php

declare(strict_types=1);

namespace Kodventure\LaravelDataExporter\Tests;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Kodventure\LaravelDataExporter\DTO\ExportedFileDTO;
use Kodventure\LaravelDataExporter\Enums\ExportFormat;
use Kodventure\LaravelDataExporter\Enums\ExportMode;
use Kodventure\LaravelDataExporter\Jobs\HandleExportJob;
use Kodventure\LaravelDataExporter\Services\DataExporter;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DataExporterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('test');

        config()->set('data-exporter.storage.disk', 'test');
        config()->set('data-exporter.storage.path', 'exports');
        config()->set('data-exporter.notifications.strategy', 'none');
    }

    public function test_csv_sync_export_successful(): void
    {
        $service = app(DataExporter::class);

        $result = $service->export(
            source: [
                ['id' => 1, 'name' => 'Ada'],
                ['id' => 2, 'name' => 'Linus'],
            ],
            mode: ExportMode::All,
            format: ExportFormat::CSV,
            page: null,
            perPage: null,
            selectedIds: [],
            user: null,
            async: false,
        );

        $this->assertInstanceOf(ExportedFileDTO::class, $result);
        $this->assertSame('csv', $result->format->value);
        Storage::disk('test')->assertExists($result->path);
    }

    public function test_xlsx_sync_export_successful(): void
    {
        $service = app(DataExporter::class);

        $result = $service->export(
            source: [
                ['id' => 1, 'name' => 'Ada'],
                ['id' => 2, 'name' => 'Linus'],
            ],
            mode: ExportMode::All,
            format: ExportFormat::XLSX,
            page: null,
            perPage: null,
            selectedIds: [],
            user: null,
            async: false,
        );

        $this->assertInstanceOf(ExportedFileDTO::class, $result);
        $this->assertSame('xlsx', $result->format->value);
        Storage::disk('test')->assertExists($result->path);
    }

    public function test_xlsx_normalizes_array_value_as_json_string(): void
    {
        $service = app(DataExporter::class);

        $result = $service->export(
            source: [
                ['id' => 1, 'meta' => ['role' => 'admin', 'active' => true]],
            ],
            mode: ExportMode::All,
            format: ExportFormat::XLSX,
            page: null,
            perPage: null,
            selectedIds: [],
            user: null,
            async: false,
        );

        $binary = Storage::disk('test')->get($result->path);
        $tmp = tempnam(sys_get_temp_dir(), 'xlsx_test_');
        file_put_contents($tmp, $binary);

        $sheet = IOFactory::load($tmp)->getActiveSheet();
        $rawValue = $sheet->getCell('B2')->getValue();

        @unlink($tmp);

        $this->assertIsString($rawValue);
        $this->assertStringContainsString('"role":"admin"', $rawValue);
    }

    public function test_async_export_job_is_queued(): void
    {
        Queue::fake();

        $service = app(DataExporter::class);

        $service->export(
            source: [
                ['id' => 1, 'name' => 'Ada'],
            ],
            mode: ExportMode::All,
            format: ExportFormat::CSV,
            page: null,
            perPage: null,
            selectedIds: [],
            user: null,
            async: true,
        );

        Queue::assertPushed(HandleExportJob::class);
    }

    public function test_unsupported_format_throws_exception(): void
    {
        $service = app(DataExporter::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported export format');

        $service->export(
            source: [
                ['id' => 1, 'name' => 'Ada'],
            ],
            mode: ExportMode::All,
            format: ExportFormat::PDF,
            page: null,
            perPage: null,
            selectedIds: [],
            user: null,
            async: false,
        );
    }

    public function test_selected_mode_with_empty_selection_throws_actionable_warning_exception(): void
    {
        $service = app(DataExporter::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Selected export mode requires at least one selected ID.');

        $service->export(
            source: [
                ['id' => 1, 'name' => 'Ada'],
            ],
            mode: ExportMode::Selected,
            format: ExportFormat::CSV,
            page: null,
            perPage: null,
            selectedIds: [],
            user: null,
            async: false,
        );
    }
}
