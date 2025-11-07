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
            ->line('Por favor, regulariza tu situación a la brevedad para evitar recargos o restricciones.')
            ->action('Ir a mi Panel', url('/alumno/dashboard'))
            ->line('Gracias por tu atención.');
    }

    /*
     * Define los datos que deben guardarse en la base de datos para la notificación.
    */
    public function toDatabase($notifiable)
    {
        // El alumno y el cargo ya están disponibles como propiedades protegidas
        return [
            'tipo' => 'adeudo', // Etiqueta para identificar el tipo de notificación
            'concepto' => $this->cargo->concepto,
            'monto' => $this->cargo->monto,
            'fecha_limite' => $this->cargo->fecha_limite->format('Y-m-d'), // Formato estándar de BD
            'alumno_nombre' => $this->alumno->nombre . ' ' . $this->alumno->apellidoP,
        ];
    }
}