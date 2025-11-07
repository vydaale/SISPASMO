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

    /*
     * Define los canales de entrega de la notificación.
    */
    public function via($notifiable): array
    {
        return ['mail', 'database']; 
    }
    
    public function shouldQueue(string $channel): bool
    {
        return $channel !== 'database'; 
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

    /*
     * Define los datos que deben guardarse en la base de datos para la notificación.
    */
    public function toDatabase($notifiable): array
    {
        return [
            'titulo'   => 'Credenciales de acceso generadas',
            'mensaje' => 'Fuiste aceptado(a) y se generaron tus credenciales de acceso.',
            'nombre'  => $this->nombreCompleto,
            'usuario' => $this->usuario,
            'password_temporal' => $this->passwordTemporal,
            'login_url' => $this->loginUrl,
        ];
    }
}
