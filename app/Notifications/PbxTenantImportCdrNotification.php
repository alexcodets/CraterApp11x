<?php

namespace Crater\Notifications;

use Crater\Models\PbxCdrTenant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PbxTenantImportCdrNotification extends Notification
{
    use Queueable;

    private PbxCdrTenant $tenant;

    private string $error;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(PbxCdrTenant $tenant, string $error)
    {
        $this->tenant = $tenant;
        $this->error = $error;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage())
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
