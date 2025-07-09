<?php

namespace Kodventure\LaravelDataExporter\Facades;

use Illuminate\Support\Facades\Facade;

class DataExporter extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'data-exporter';
    }
}