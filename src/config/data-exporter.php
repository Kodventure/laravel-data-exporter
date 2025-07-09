<?php

return [
    'builders' => [
        // \App\Models\User::class => \App\ExportQueryBuilders\UserExportQueryBuilder::class,
        // \App\Models\Product::class => \App\ExportQueryBuilders\ProductExportQueryBuilder::class,
    ],
    'storage' => [
        'disk' => 'public',
        'path' => 'exports',
    ],
    'files' => [
        'chunk_size' => 1000,
    ]     
];