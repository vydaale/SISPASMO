<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alumno;
use App\Notifications\AlertaAdeudo;
use Carbon\Carbon;

class NotificarAdeudosCommand extends Command
{
    protected $signature = 'adeudos:notificar';
    protected $description = 'Notifica a los alumnos que no han pagado su colegiatura del mes anterior.';

    public function handle()
    {
        $hoy = Carbon::now();
        $inicioMesAnterior = $hoy->copy()->subMonth()->startOfMonth();
        $finMesAnterior = $hoy->copy()->subMonth()->endOfMonth();

        // Ejecutar solo 3 días después de terminar el mes
        //if ($hoy->isSameDay($finMesAnterior->copy()->addDays(3))) {
            $this->info('Verificando adeudos del mes anterior...');

            // Buscar alumnos sin recibos en ese rango
            $alumnosConAdeudos = Alumno::whereDoesntHave('recibos', function ($q) use ($inicioMesAnterior, $finMesAnterior) {
                $q->whereBetween('fecha_pago', [$inicioMesAnterior, $finMesAnterior]);
            })->get();

            foreach ($alumnosConAdeudos as $alumno) {
                $usuario = $alumno->usuario;
                
                if (!$usuario || !$usuario->correo) {
                    continue; // sin correo, no se notifica
                }

                // Puedes crear un cargo "genérico" del mes anterior si lo manejas así
                $cargo = (object)[
                    'concepto' => 'Pago mensual de ' . $inicioMesAnterior->format('F Y'),
                    'monto' => 0,
                    'fecha_limite' => $finMesAnterior,
                ];

                $usuario->notify(new AlertaAdeudo($usuario, $cargo));

                $this->info("Correo de adeudo enviado a: {$usuario->correo}");
            }
        //} else {
         //   $this->info('No es el día de envío de notificaciones aún.');
        //}

        return 0;
    }
}
