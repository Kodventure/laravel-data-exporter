<?php

namespace Kodventure\LaravelDataExporter\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Kodventure\LaravelDataExporter\Contracts\ShouldBeNotifiableInterface;
use Kodventure\LaravelDataExporter\DTO\ExportedFileDTO;
use Kodventure\LaravelDataExporter\Notifications\ExportReadyNotification;
use Kodventure\LaravelDataExporter\Contracts\ExportSourceInterface;
use Kodventure\LaravelDataExporter\Exporters\BaseExporter;
use Illuminate\Support\Facades\Config;

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
    public function handle(): ExportedFileDTO
    {
        $exported = $this->exporter->export($this->exportSource);

        if ($this->user && $this->shouldNotifyCompleted()) {
            $this->user->notify(ExportReadyNotification::completed($exported));
        }

        return $exported;
    }

    private function shouldNotifyCompleted(): bool
    {
        $strategy = (string) Config::get('data-exporter.notifications.strategy', 'started_and_completed');
        $allowed = ['none', 'started_only', 'completed_only', 'started_and_completed'];

        if (!in_array($strategy, $allowed, true)) {
            $strategy = 'started_and_completed';
        }

        return in_array($strategy, ['completed_only', 'started_and_completed'], true);
    }
}
