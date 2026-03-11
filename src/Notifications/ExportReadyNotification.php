<?php

namespace Kodventure\LaravelDataExporter\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Foundation\Queue\Queueable;
use Kodventure\LaravelDataExporter\DTO\ExportedFileDTO;
use Kodventure\LaravelDataExporter\Enums\ExportFormat;

class ExportReadyNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
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

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
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

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'status' => $this->status,
            'message' => $this->status === 'started'
                ? 'Export started'
                : 'Export completed',
            'file' => [
                'name' => $this->name,
                'format' => $this->format,
                'downloadUrl' => $this->downloadUrl,
                'temporaryUrl' => $this->temporaryUrl,
                'size' => $this->size,
            ],
        ];
    }


    

    // public function toFilamentNotification($notifiable): FilamentFormat
    // {
    //
    // FilamentNotification::make()
    // ->title('Export dosyanız indirilmeye hazır')
    // ->success()
    // ->persistent()
    // ->send();  
    //
    //     $url = url('/storage/' . $this->filename);
    //     logger("to filament...");
    //     return new FilamentFormat(
    //         title: 'Export hazır',
    //         body: 'Export dosyanız indirilmeye hazır.',
    //         icon: 'heroicon-o-document-text',
    //         iconColor: 'primary',
    //         actions: [
    //             [
    //                 'name' => 'Dosyayı indir',
    //                 'url' => $url,
    //                 'color' => 'primary',
    //             ],
    //         ],
    //     );
    // }    

    // public function toBroadcast($notifiable)
    // {
    //     $this->notifiable = $notifiable;

    //     logger("to broadcast...");
    //     return new BroadcastMessage(
    //         $this->toFilamentNotification($notifiable)->toArray()
    //     );
    // }   

    // public function broadcastOn(): Channel
    // {
    //     return new PrivateChannel('admin.'.$this->notifiable->id);
    // }

    // public function broadcastOn(): array
    // {
    //     return [
    //         new Channel('system-maintenance'),
    //     ];
    // }

    // public function broadcastAs(): string
    // {
    //     return 'SystemMaintenanceEvent'; // echo'nun dinleyeceği metin , Admin/SystemMaintenanceEvent veya .SystemMaintenanceEvent diye gidiyor dikkat!
    // }      
}
