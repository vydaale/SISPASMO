<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/*
 * Notificación enviada al Aspirante/Alumno al ser aceptado para proporcionarle sus credenciales
    de acceso, incluyendo su matrícula como nuevo usuario y una contraseña temporal.
*/
class CredencialesAspirante extends Notification
{
    use Queueable;

    /*
     * Define las propiedades necesarias para construir el mensaje.
        La sintaxis promocionada en el constructor asigna automáticamente los argumentos a propiedades públicas.
    */
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
    
    /*
     * Determina si la notificación debe ser encolada (puesta en cola) para un canal específico.
        Se usa para enviar el correo de forma asíncrona, mientras que el registro en ña base de datos se 
        hace de forma síncrona para que aparezca inmediatamente.
    */
    public function shouldQueue(string $channel): bool
    {
        return $channel !== 'database'; 
    }

    /*
     * Construye el mensaje de correo electrónico.
    */
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
