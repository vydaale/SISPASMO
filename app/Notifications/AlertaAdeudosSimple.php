<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AlertaAdeudoSimple extends Notification
{
    use Queueable;

    public function __construct(
        public string $alumnoNombre,
        public string $concepto,        // Inscripción, Mensualidad, Taller, etc.
        public string $monto,           // '$1,200.00'
        public string $fechaLimite,     // 'YYYY-MM-DD'
        public bool   $vencido = false, // true si ya venció
        public ?int   $idPago = null,   // para identificar el adeudo en notis
        public ?string $urlPago = null  // liga para pagar / ver detalle en SISPASMO
    ) {}

    public function via($notifiable): array
    {
        return ['database','mail']; // in-app y por correo
    }

    public function toMail($notifiable): MailMessage
    {
        $titulo = $this->vencido ? '⚠️ Adeudo VENCIDO' : 'Recordatorio de pago';
        $mail = (new MailMessage)
            ->subject("{$titulo}: {$this->concepto}")
            ->greeting("Hola, {$this->alumnoNombre}")
            ->line("Concepto: **{$this->concepto}**")
            ->line("Monto pendiente: **{$this->monto}**")
            ->line("Fecha límite: **{$this->fechaLimite}**");

        if ($this->vencido) {
            $mail->line('Este pago se encuentra VENCIDO. Favor de regularizarte a la brevedad.');
        } else {
            $mail->line('Este es un recordatorio previo a la fecha límite.');
        }

        if ($this->urlPago) {
            $mail->action('Ver / Realizar pago', $this->urlPago);
        }

        return $mail->line('Si ya realizaste el pago, ignora este mensaje.');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'tipo'        => $this->vencido ? 'adeudo_vencido' : 'adeudo_por_vencer',
            'titulo'      => $this->vencido ? "Adeudo vencido: {$this->concepto}" : "Recordatorio: {$this->concepto}",
            'mensaje'     => "Monto: {$this->monto}. Límite: {$this->fechaLimite}.",
            'id_pago'     => $this->idPago,
            'concepto'    => $this->concepto,
            'monto'       => $this->monto,
            'fecha_limite'=> $this->fechaLimite,
            'url'         => $this->urlPago,
        ];
    }
}
