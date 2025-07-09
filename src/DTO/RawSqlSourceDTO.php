<?php

namespace Kodventure\LaravelDataExporter\DTO;

class RawSqlSourceDTO
{
    public function __construct(
        public string $sql,
        public array $bindings = [],
        public ?string $modelClass = null, // opsiyonel, headers için kullanılabilir
    ) {}
}
