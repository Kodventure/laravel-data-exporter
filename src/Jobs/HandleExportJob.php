<?php

namespace Kodventure\LaravelDataExporter\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Kodventure\LaravelDataExporter\Contracts\ShouldBeNotifiableInterface;
use Kodventure\LaravelDataExporter\Notifications\ExportReadyNotification;
use Kodventure\LaravelDataExporter\Contracts\ExportSourceInterface;
use Kodventure\LaravelDataExporter\Exporters\BaseExporter;

/**
 * Export işlemini yöneten job.
 * Tüm iş mantığı builder ve manager katmanlarına delege edilmiştir.
 * Bu job sadece orchestration (akış) görevini üstlenir.
 */
class HandleExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public ExportSourceInterface $exportSource, public BaseExporter $exporter, public ?ShouldBeNotifiableInterface $user){}

    /**
     * Execute the job.
     */
    public function handle()
    {
        $exported = $this->exporter->export($this->exportSource);

        if($this->user){
            $this->user->notify(new ExportReadyNotification(
                // name: $exported->name,     
                // format: $exported->format,
                // downloadUrl: $exported->url,
                // temporaryUrl: $exported->temporaryUrl,
                // size: $exported->size,
            ));        
        }
    }
}
