<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define los comandos de Artisan de la aplicación.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Define el schedule de comandos.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('adeudos:notificar')->dailyAt('08:00');
    }
}
