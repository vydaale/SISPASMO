<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cargo;
use App\Notifications\AlertaAdeudo;

class EnviarAlertasAdeudos extends Command
{
    protected $signature = 'alertas:adeudos';
    protected $description = 'Envía notificaciones de adeudos vencidos o próximos a vencer';

    public function handle()
    {
        $this->info('Buscando adeudos pendientes...');

        $hoy = now();

        $adeudos = Cargo::with('alumno.usuario')
            ->where('estatus', 'pendiente')
            ->whereDate('fecha_limite', '<=', $hoy)
            ->get();

        foreach ($adeudos as $cargo) {
            $usuario = $cargo->alumno->usuario;
            $usuario->notify(new AlertaAdeudo($cargo->alumno, $cargo));
        }

        $this->info('Se enviaron ' . $adeudos->count() . ' alertas de adeudo.');
    }
}