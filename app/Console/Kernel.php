<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Ejecutar el comando de notificaciones
        $schedule->command('notificaciones:vacunas')->everyTenMinutes();
        $schedule->command('notificaciones:previas')->dailyAt('04:00'); 
        $schedule->command('notificaciones:promos-nuevas')->dailyAt('04:00'); 

    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
