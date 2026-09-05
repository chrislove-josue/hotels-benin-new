<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $plainPassword,
        public string $magicUrl,
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Bienvenue sur Hôtels Bénin — vos identifiants de connexion')
            ->view('emails.account-created', [
                'user' => $notifiable,
                'password' => $this->plainPassword,
                'magicUrl' => $this->magicUrl,
            ]);
    }
}
