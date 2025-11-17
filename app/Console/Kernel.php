<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Leer configuraciones desde la BD (tipo y fecha_envio)
        $configs = \DB::table('notificaciones')
            ->select('tipo', 'fecha_envio')
            ->groupBy('tipo', 'fecha_envio')
            ->get();

        foreach ($configs as $config) {

            // Si fecha_envio = "10min" → everyTenMinutes()
            if ($config->fecha_envio === '10min') {
                $schedule->command("notificaciones:{$config->tipo}")
                         ->everyTenMinutes();
                continue;
            }

            // Si fecha_envio es una hora tipo "04:00", "07:30", etc → dailyAt()
            if (preg_match('/^\d{2}:\d{2}$/', $config->fecha_envio)) {
                $schedule->command("notificaciones:{$config->tipo}")
                         ->dailyAt($config->fecha_envio);
                continue;
            }
        }
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
