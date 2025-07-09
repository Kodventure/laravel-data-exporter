<?php 

namespace Kodventure\LaravelDataExporter\Providers;

use Illuminate\Support\ServiceProvider;
use Kodventure\LaravelDataExporter\Commands\MakeExportQueryBuilderCommand;
use Kodventure\LaravelDataExporter\Services\DataExporter;

class DataExporterServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // php artisan vendor:publish --tag=data-exporter-config
        // Konfigürasyon dosyasını publish etme
        $this->publishes([
            __DIR__.'/../config/data-exporter.php' => config_path('data-exporter.php'),
        ], 'data-exporter-config');
    }

    public function register()
    {
        // Komutları kaydet
        $this->commands([
            MakeExportQueryBuilderCommand::class,
        ]);

        $this->app->singleton(DataExporter::class, function ($app) {
            return new DataExporter();
        });        

        $this->app->singleton('data-exporter', function ($app) {
            return new DataExporter();
        });        

        $this->mergeConfigFrom(
            __DIR__.'/../config/data-exporter.php', 'data-exporter'
        );        
    }
}
