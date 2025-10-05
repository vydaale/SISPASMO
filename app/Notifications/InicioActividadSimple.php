<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class InicioActividadSimple extends Notification
{
    use Queueable;

    public function __construct(
        public string $nombreActividad,
        public string $fecha,       // 'YYYY-MM-DD'
        public string $hora,        // 'HH:MM'
        public string $lugar,       // aula o enlace
        public string $docente,     // nombre del docente
        public ?string $instrucciones = null,
        public ?string $urlDetalle = null
    ) {}

    public function via($notifiable): array
    {
        return ['mail']; // in-app + correo
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Inicio: {$this->nombreActividad}")
            ->greeting("Hola, {$notifiable->nombre}")
            ->line("Tu actividad **{$this->nombreActividad}** inicia el **{$this->fecha}** a las **{$this->hora}**.")
            ->line("Lugar/modalidad: {$this->lugar}")
            ->line("Docente: {$this->docente}");

        if ($this->instrucciones) $mail->line("Indicaciones: {$this->instrucciones}");
        if ($this->urlDetalle)    $mail->action('Ver detalles', $this->urlDetalle);

        return $mail->line('¡Te esperamos puntual!');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'titulo'        => "Inicio: {$this->nombreActividad}",
            'mensaje'       => "Fecha: {$this->fecha} {$this->hora}. Lugar: {$this->lugar}. Docente: {$this->docente}",
            'instrucciones' => $this->instrucciones,
            'url'           => $this->urlDetalle,
        ];
    }
}
