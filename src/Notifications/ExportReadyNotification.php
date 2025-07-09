<?php

namespace Kodventure\LaravelDataExporter\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Foundation\Queue\Queueable;

class ExportReadyNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        // public string $filename,
        // public string $format,
        // public string $downloadUrl // s3 linki gibi
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
        return ['mail','database','broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('The export file is ready.'))
            ->line(__('The export file is ready.'))
            ->action(__('Download'), url('/'))
            ->line(__('Thank you for using our application!'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
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
