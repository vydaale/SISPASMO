<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CredencialesAspirante extends Notification
{
    use Queueable;

    public function __construct(
        public string $nombreCompleto,
        public string $usuario,
        public string $passwordTemporal,
        public string $loginUrl
    ) {}

    public function via($notifiable): array
    {
        return ['mail']; // si quieres también in-app: ['mail','database']
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tus credenciales de acceso a SISPASMO')
            ->greeting("Hola, {$this->nombreCompleto}")
            ->line('Has sido aceptado(a). Estas son tus credenciales de acceso:')
            ->line("**Usuario:** `{$this->usuario}`")
            ->line("**Contraseña temporal:** `{$this->passwordTemporal}`")
            ->action('Ingresar al sistema', $this->loginUrl)
            ->line('Por seguridad, te recomendamos cambiar la contraseña al ingresar.');
    }
}
