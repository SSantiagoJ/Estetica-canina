<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Reserva;
use App\Notifications\RecordatorioVacuna;
use Carbon\Carbon;

class EnviarNotificacionVacuna extends Command
{
    protected $signature = 'notificaciones:vacunas';
    protected $description = 'Enviar notificaciones a usuarios con vacuna antirrábica vencida o próxima';

    public function handle()
{
    $hoy = now();
    $umbral = $hoy->subMonths(11);

    $reservas = \App\Models\Reserva::whereHas('detalles.servicio', function ($q) {
            $q->where('nombre_servicio', 'Vacuna Antirrábica');
        })
        ->whereDate('fecha', '<=', now())

        ->get();

    $this->info('Reservas encontradas: ' . $reservas->count());

    foreach ($reservas as $reserva) {
        $usuario = $reserva->usuario;
        $this->info('Enviando correo a: ' . $usuario->correo);
        $usuario->notify(new \App\Notifications\RecordatorioVacuna($reserva));
    }

    $this->info('✅ Notificaciones de vacuna enviadas correctamente.');
}

}
