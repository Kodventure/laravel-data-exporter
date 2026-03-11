<?php

namespace Kodventure\LaravelDataExporter\Notifications;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Foundation\Queue\Queueable;
use Kodventure\LaravelDataExporter\DTO\ExportedFileDTO;
use Kodventure\LaravelDataExporter\Enums\ExportFormat;

class ExportReadyNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(
        public string $status,
        public ?string $name = null,
        public ?string $format = null,
        public ?string $downloadUrl = null,
        public ?string $temporaryUrl = null,
        public ?int $size = null,
    ) {}

    public static function started(ExportFormat $format): self
    {
        return new self(
            status: 'started',
            format: $format->value,
        );
    }

    public static function completed(ExportedFileDTO $exportedFile): self
    {
        return new self(
            status: 'completed',
            name: $exportedFile->name,
            format: $exportedFile->format->value,
            downloadUrl: $exportedFile->url,
            temporaryUrl: $exportedFile->temporaryUrl,
            size: $exportedFile->size,
        );
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->status === 'started'
            ? 'Export started'
            : 'The export file is ready.';

        $line = $this->status === 'started'
            ? 'Your export request has been queued.'
            : 'Your export file is ready for download.';

        $mail = (new MailMessage)
            ->subject($subject)
            ->line($line);

        if ($this->downloadUrl) {
            $mail->action('Download', $this->downloadUrl);
        }

        return $mail;
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }

    /**
     * Filament notification bell compatibility.
     * Requires 'format' => 'filament' for Filament's DatabaseNotifications query filter.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if ($this->status === 'started') {
            return [
                'format' => 'filament',
                'title'  => 'Export started',
                'body'   => 'Your export request has been queued. You will be notified when the file is ready.',
                'status' => 'info',
            ];
        }

        $url = $this->temporaryUrl ?? $this->downloadUrl;

        $data = [
            'format' => 'filament',
            'title'  => 'Export ready: ' . strtoupper((string) $this->format),
            'body'   => 'Your export file is ready for download.' . ($this->name ? ' (' . $this->name . ')' : ''),
            'status' => 'success',
        ];

        if ($url) {
            $data['actions'] = [
                [
                    'name'                  => 'download',
                    'label'                 => 'Download',
                    'url'                   => $url,
                    'color'                 => 'success',
                    'shouldOpenUrlInNewTab' => true,
                ],
            ];
        }

        return $data;
    }
}