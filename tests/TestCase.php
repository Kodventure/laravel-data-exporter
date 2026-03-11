<?php

declare(strict_types=1);

namespace Kodventure\LaravelDataExporter\Tests;

use Kodventure\LaravelDataExporter\Providers\DataExporterServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            DataExporterServiceProvider::class,
        ];
    }
}
