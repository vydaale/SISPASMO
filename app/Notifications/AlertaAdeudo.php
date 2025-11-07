<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


/*
 * Notificación enviada por correo electrónico al Alumno cuando tiene un cargo vencido
 * o próximo a vencer (generado por el comando de consola). Implementa ShouldQueue para procesarse en 
    segundo plano (asíncrono).
*/
class AlertaAdeudo extends Notification implements ShouldQueue
{
    use Queueable;

    protected $alumno;
    protected $cargo;

    /*
     * Crea una nueva instancia de notificación.
    */
    public function __construct($alumno, $cargo)
    {
        $this->alumno = $alumno;
        $this->cargo = $cargo;
    }

    /*
     * Define los canales de entrega de la notificación.
    */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }


    /*
     * Construye el mensaje de correo electrónico.
     *
     * Se utiliza la información del Cargo para detallar el concepto, monto y fecha límite.
        Incluye un action button para redirigir al panel del alumno.
    */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Alerta de adeudo pendiente')
            ->greeting('Hola ' . $this->alumno->nombre . ' ' . $this->alumno->apellidoP)
            ->line('Te informamos que tienes un adeudo pendiente con la institución.')
            ->line('*Concepto:* ' . $this->cargo->concepto)
            ->line('*Monto:* $' . number_format($this->cargo->monto, 2))
            ->line('*Fecha límite de pago:* ' . $this->cargo->fecha_limite->format('d/m/Y'))
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