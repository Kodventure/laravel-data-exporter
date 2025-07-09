<?php 

namespace Kodventure\LaravelDataExporter\Factories;

use Kodventure\LaravelDataExporter\Contracts\ExportQueryBuilderInterface;

class ExportQueryBuilderFactory
{
    protected static array $customBuilders = [];

    /**
     * Kullanıcıların kendi builder'larını kaydedebileceği method.
     */
    public static function registerBuilder(string $model, string $builder): void
    {
        self::$customBuilders[$model] = $builder;
    }

    /**
     * Model adına göre uygun ExportQueryBuilder döndürür.
     */
    public static function make(string $model): ExportQueryBuilderInterface
    {
        // Önce konfigürasyondan alınan builder'ları kontrol et
        $builders = config('data-exporter.builders');

        if (isset($builders[$model])) {
            return app($builders[$model]);
        }

        // Kullanıcının kaydettiği özel eşleştirmeleri kontrol et
        if (isset(self::$customBuilders[$model])) {
            return app(self::$customBuilders[$model]);
        }

        throw new \Exception(__('There is no proper query builder for this model: :model', ['model' => $model]));
    }
}
