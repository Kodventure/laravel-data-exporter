<?php

namespace Kodventure\LaravelDataExporter\Contracts;

interface ExportQueryBuilderInterface
{
    /**
     * Export için query oluşturur.
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function build(array $params): \Illuminate\Database\Eloquent\Builder;
}
