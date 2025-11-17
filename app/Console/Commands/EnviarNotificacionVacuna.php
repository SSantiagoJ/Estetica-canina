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

    $hoy = \Carbon\Carbon::now();
    $umbral = $hoy->copy()->subMonths(11); // hace 11 meses

    // Buscar reservas de vacuna antirrábica usando fecha_creacion DE DETALLE
    $reservas = \App\Models\Reserva::whereHas('detalles', function ($q) use ($umbral) {
        $q->where('id_servicio', 4) // Vacuna antirrábica
          ->whereDate('fecha_creacion', '<=', $umbral); // ← AQUÍ EL CAMBIO
    })
    ->get();

    $this->info('Reservas encontradas: ' . $reservas->count());

    foreach ($reservas as $reserva) {

        $usuario = $reserva->usuario;

        if (!$usuario || !$usuario->correo) {
            $this->warn("⚠️ Usuario {$reserva->id_usuario} sin correo registrado.");
            continue;
        }

        try {
            // Extraemos la notificación desde la BD
            $notificacionBD = \DB::table('notificaciones')
                ->where('tipo', 'vacunas')
                ->first();

            \Illuminate\Support\Facades\Notification::route('mail', $usuario->correo)
                ->notify(new \App\Notifications\RecordatorioVacuna(
                    $reserva,
                    $usuario,
                    $notificacionBD
                ));

            $this->info("✅ Recordatorio enviado a {$usuario->correo}");
        } catch (\Exception $e) {
            $this->error("❌ Error enviando a {$usuario->correo}: " . $e->getMessage());
        }
    }

    $this->info('🎉 Proceso completado correctamente.');
}
}