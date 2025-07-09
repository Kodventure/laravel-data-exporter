<?php 

namespace Kodventure\LaravelDataExporter\Filters;

use Kodventure\LaravelDataExporter\Contracts\FilterNormalizerInterface;

class FilamentFilterNormalizer implements FilterNormalizerInterface
{

    public static function normalize(array $filters): array
    {
        $normalized = [];

        foreach ($filters as $key => $value) {
            if (isset($value['values'])) {
                $normalized[$key] = $value['values'];
            } elseif (isset($value['value'])) {
                $normalized[$key] = $value['value'];
            } elseif (isset($value['isActive'])) {
                $normalized[$key] = $value['isActive'] ? 1 : 0;
            } else {
                $normalized[$key] = $value;
            }
        }

        return $normalized;
    }    
}
