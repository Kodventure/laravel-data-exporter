<?php

namespace Kodventure\LaravelDataExporter\Contracts;

interface FilterNormalizerInterface
{
    public static function normalize(array $rawFilters): array;
}