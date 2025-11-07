<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlertaAdeudo extends Notification implements ShouldQueue
{
    use Queueable;

    protected $alumno;
    protected $cargo;

    public function __construct($alumno, $cargo)
    {
        $this->alumno = $alumno;
        $this->cargo = $cargo;
    }

    public function via($notifiable)
    {
        return ['mail','database']; // Canal de envío: correo
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Alerta de Adeudo Pendiente')
            ->greeting('Hola ' . $this->alumno->nombre . ' ' . $this->alumno->apellidoP)
            ->line('Te informamos que tienes un adeudo pendiente con la institución.')
            ->line('**Concepto:** ' . $this->cargo->concepto)
            ->line('**Monto:** $1,300' )
            ->line('**Fecha límite de pago:** ' . $this->cargo->fecha_limite->format('d/m/Y'))
            ->line('Por favor, regulariza tu situación a la brevedad para evitar recargos o restricciones.')
            ->action('Ir a mi Panel', url('/alumno/dashboard'))
            ->line('Gracias por tu atención.');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'titulo'   => 'Alerta de Adeudo Pendiente',
            'mensaje' => 'Tienes un adeudo pendiente por ' . $this->cargo->concepto . ' por un monto de $1,100. Fecha límite de pago: ' . $this->cargo->fecha_limite->format('d/m/Y') . '.',
            'concepto' => $this->cargo->concepto,
            'monto'    => 1300,
            'fecha_limite' => $this->cargo->fecha_limite,
        ];
    }
}
