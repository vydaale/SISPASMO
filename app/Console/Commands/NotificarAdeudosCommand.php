<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alumno;
use App\Notifications\AlertaAdeudo;
use Carbon\Carbon;

/*
 * Comando de consola para identificar y notificar a los alumnos con pagos pendientes.
    Revisa los alumnos que no registraron un recibo de pago durante el mes anterior y les envía una alerta de adeudo.
*/
class NotificarAdeudosCommand extends Command
{
    /*
     * El nombre y la firma del comando de consola.
     *
     * @var string
    */
    protected $signature = 'adeudos:notificar';
    protected $description = 'Notifica a los alumnos que no han pagado su colegiatura del mes anterior.';

    /*
     * Ejecuta el comando de consola.
    */
    public function handle()
    {
        $hoy = Carbon::now();
        /* Definición del rango del mes anterior usando Carbon. */
        $inicioMesAnterior = $hoy->copy()->subMonth()->startOfMonth();
        $finMesAnterior = $hoy->copy()->subMonth()->endOfMonth();

            $this->info('Verificando adeudos del mes anterior.');

            /* Trae alumnos que NO tienen recibos en el rango del mes anterior. */
            $alumnosConAdeudos = Alumno::whereDoesntHave('recibos', function ($q) use ($inicioMesAnterior, $finMesAnterior) {
                $q->whereBetween('fecha_pago', [$inicioMesAnterior, $finMesAnterior]);
            })->get();

            foreach ($alumnosConAdeudos as $alumno) {
                $usuario = $alumno->usuario;
                
                if (!$usuario || !$usuario->correo) {
                    continue; 
                }

                /* Creación de un objeto "Cargo" temporal para simular el adeudo en la notificación. */
                $cargo = (object)[
                    'concepto' => 'Pago mensual de ' . $inicioMesAnterior->format('F Y'),
                    'monto' => 0,
                    'fecha_limite' => $finMesAnterior,
                ];

                /* Envío de la notificación AlertaAdeudo al usuario del alumno. */
                $usuario->notify(new AlertaAdeudo($usuario, $cargo));

                $this->info("Correo de adeudo enviado a: {$usuario->correo}");
            }

        return 0;
    }
}
