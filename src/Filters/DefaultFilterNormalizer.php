<?php 

namespace Kodventure\LaravelDataExporter\Filters;

use Kodventure\LaravelDataExporter\Contracts\FilterNormalizerInterface;

class DefaultFilterNormalizer implements FilterNormalizerInterface
{
    /*
    *  İç içe geçmiş filtreleri düzleştirir.
    */
    public static function normalize(array $filters): array
    {
        return $filters;
    }
}
