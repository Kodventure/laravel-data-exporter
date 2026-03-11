<?php

return [
    'builders' => [
        // \App\Models\User::class => \App\ExportQueryBuilders\UserExportQueryBuilder::class,
        // \App\Models\Product::class => \App\ExportQueryBuilders\ProductExportQueryBuilder::class,
    ],
    'storage' => [
        'disk' => 'public',
        'path' => 'exports',
        'ttl_minutes' => 30, // temporary URL TTL in minutes
    ],
    'files' => [
        'chunk_size' => 1000,
    ],
    'notifications' => [
        // Allowed: none, started_only, completed_only, started_and_completed
        'strategy' => getenv('DATA_EXPORTER_NOTIFICATION_STRATEGY') ?: 'started_and_completed',
    ],
];