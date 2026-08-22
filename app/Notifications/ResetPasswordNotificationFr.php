<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotificationFr extends ResetPassword
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Réinitialisation de votre mot de passe — Hôtels Bénin')
            ->view('emails.reset-password', [
                'url' => $this->resetUrl($notifiable),
                'user' => $notifiable,
            ]);
    }
}
