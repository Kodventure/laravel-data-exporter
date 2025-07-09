<?php 

namespace Kodventure\LaravelDataExporter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeExportQueryBuilderCommand extends Command
{
    protected $signature = 'make:export-query-builder {model}';

    protected $description = 'Create a new ExportQueryBuilder for the given model';

    public function handle()
    {
        $model = $this->argument('model');
        $builderName = $this->getBuilderClassName($model);
        $folderName = "DataExportQueryBuilders";
        $namespace = "App\\$folderName";

        // Sınıfın tam adı
        $fullClassName = $namespace . '\\' . $builderName;

        if (class_exists($fullClassName)) {
            $this->error("Builder already exists: $fullClassName");
            return;
        }

        // Sınıfın oluşturulacağı dizini belirleyin
        $directory = app_path($folderName);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        // Builder sınıfı için dosya yolu
        $filePath = $directory . '/' . $builderName . '.php';

        // Sınıf şablonunu oluştur
        $stub = $this->getStub($model);

        // Dosyayı yaz
        file_put_contents($filePath, $stub);

        $this->info("ExportQueryBuilder class created successfully: $filePath");
    }

    protected function getBuilderClassName($model)
    {
        return Str::studly(class_basename($model)) . 'ExportQueryBuilder';
    }

    protected function getStub($model)
    {
        return "<?php

namespace App\\DataExportQueryBuilders;

use Kodventure\\LaravelDataExporter\\Contracts\\ExportQueryBuilderInterface;
use \\$model;

class " . $this->getBuilderClassName($model) . " implements ExportQueryBuilderInterface
{
    public function build(array \$params): \\Illuminate\\Database\\Eloquent\\Builder
    {
        \$query = ".class_basename($model)."::query();

        // You can apply additional query logic based on \$params or other factors

        return \$query;
    }
}
";
    }
}
