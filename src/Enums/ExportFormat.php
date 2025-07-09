<?php

namespace Kodventure\LaravelDataExporter\Enums;

enum ExportFormat: string
{
    case CSV = 'csv';
    case XLSX = 'xlsx';
    case JSON = 'json';
    case PDF = 'pdf';   // future
    case SQL = 'sql';   // future

    public function mimeType(): string
    {
        return match ($this) {
            self::CSV => 'text/csv',
            self::XLSX => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            self::JSON => 'application/json',
            self::PDF => 'application/pdf',
            self::SQL => 'text/plain',
        };
    }

    public static function labels(): array
    {
        return [
            self::CSV->value => __('CSV'),
            self::XLSX->value => __('XLSX'),
            self::JSON->value => __('JSON'),
            self::PDF->value => __('PDF'),
            self::SQL->value => __('SQL'),
        ];
    }    

    public function label(): string
    {
        return $this->name;
    }    

    public function extension(): string
    {
        return '.' . $this->value;
    }        
}