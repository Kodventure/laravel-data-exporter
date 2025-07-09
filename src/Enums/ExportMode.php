<?php

namespace Kodventure\LaravelDataExporter\Enums;

enum ExportMode: string
{
    case Page = 'page';
    case All = 'all';
    case Selected = 'selected';

    public static function labels(): array
    {
        return [
            self::Page->value => __('This page'),
            self::All->value => __('All records'),
            self::Selected->value => __('Selected rows'),
        ];
    }    

    public function label(): string
    {
        return match ($this) {
            self::Page => __('This page'),
            self::All => __('All records'),
            self::Selected => __('Selected rows'),
        };
    }    
}