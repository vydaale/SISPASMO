<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;


/*
 * Notificación utilizada para enviar el enlace de restablecimiento de contraseña al usuario.
    Nota: Esta notificación utiliza el token generado por el sistema de autenticación de Laravel.
*/
class ResetPasswordNotification extends Notification
{
    public $token;

    /*
     * Crea una nueva instancia de notificación.
    */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /*
     * Define los canales de entrega de la notificación.
    */
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        /* URL a la que se dirige el botón "Restablecer Contraseña". */
        $url = url(route('password.reset', ['token' => $this->token,'email' => $notifiable->getEmailForPasswordReset(),], false));

        return (new MailMessage)
            ->subject('Recuperación de contraseña - SISPASMO')
            ->greeting('¡Hola ' . $notifiable->nombre . '!')
            ->line('Recibimos una solicitud para restablecer la contraseña de tu cuenta en SISPASMO.')
            ->action('Restablecer contraseña', $url)
            ->line('Este enlace expirará en 60 minutos por motivos de seguridad.')
            ->line('Si no realizaste esta solicitud, puedes ignorar este correo.')
            ->salutation('Atentamente, el equipo de SISPASMO');
    }
}
