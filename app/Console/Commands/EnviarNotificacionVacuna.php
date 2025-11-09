<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Reserva;
use App\Notifications\RecordatorioVacuna;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class EnviarNotificacionVacuna extends Command
{
    protected $signature = 'notificaciones:vacunas';
    protected $description = 'Enviar recordatorios a usuarios cuya vacuna antirrábica está próxima a vencerse';

    public function handle()
    {
        date_default_timezone_set('America/Lima');

        $hoy = Carbon::now();
        $umbral = $hoy->copy()->subMonths(11); // hace 11 meses o más

        // Buscar reservas de vacuna antirrábica hace casi 1 año
        $reservas = Reserva::whereHas('detalles.servicio', function ($q) {
                $q->where('nombre_servicio', 'Vacuna Antirrábica');
            })
            ->whereDate('fecha', '<=', $umbral)
            ->get();

        $this->info('Reservas encontradas: ' . $reservas->count());

        foreach ($reservas as $reserva) {
            $usuario = $reserva->usuario;

            if (!$usuario || !$usuario->correo) {
                $this->warn("⚠️ Usuario {$reserva->id_usuario} sin correo registrado.");
                continue;
            }

            try {
                Notification::route('mail', $usuario->correo)
                    ->notify(new RecordatorioVacuna($reserva, $usuario));

                $this->info("✅ Recordatorio enviado a {$usuario->correo}");
            } catch (\Exception $e) {
                $this->error("❌ Error enviando a {$usuario->correo}: " . $e->getMessage());
            }
        }

        $this->info('🎉 Proceso completado correctamente.');
    }
}
