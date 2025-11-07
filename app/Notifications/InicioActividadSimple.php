<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/*
 * Notificación enviada a los Alumnos para anunciar el inicio, la convocatoria o
    la modificación de una actividad extracurricular (Taller o Práctica).
*/
class InicioActividadSimple extends Notification
{
    use Queueable;

    /*
     * Define las propiedades necesarias para construir el mensaje.
    */
    public function __construct(
        public string $nombreActividad,
        public string $fecha,
        public string $hora,
        public string $lugar,
        public string $docente,
        public ?string $instrucciones = null,
        public ?string $urlDetalle = null
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
        Se encola solo el correo ('mail') para no retrasar el registro en la base de datos ('database').
    */
    public function shouldQueue($notifiable, string $channel): bool
    {
        return $channel === 'mail';
    }

    /*
     * Construye el mensaje de correo electrónico.
    */
    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Inicio: {$this->nombreActividad}")
            ->greeting("Hola, {$notifiable->nombre}")
            ->line("Únete a la actividad de **{$this->nombreActividad}** inicia el **{$this->fecha}** a las **{$this->hora}**.")
            ->line("Lugar/modalidad: {$this->lugar}")
            ->line("Docente: {$this->docente}");

        if ($this->instrucciones) $mail->line("Indicaciones: {$this->instrucciones}");
        if ($this->urlDetalle)    $mail->action('Ver detalles', $this->urlDetalle);

        return $mail->line('¡Te esperamos puntual!');
    }

    /*
     * Define los datos que deben guardarse en la base de datos para la notificación.
    */
    public function toDatabase($notifiable): array
    {
        return [
            'titulo' => "Inicio: {$this->nombreActividad}",
            'mensaje' => "Fecha: {$this->fecha} {$this->hora}. Lugar: {$this->lugar}. Docente: {$this->docente}",
            'instrucciones' => $this->instrucciones,
            'url' => $this->urlDetalle,
        ];
    }
}
