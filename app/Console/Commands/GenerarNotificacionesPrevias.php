<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use App\Notifications\RecordatorioReserva;


class GenerarNotificacionesPrevias extends Command
{
    protected $signature = 'notificaciones:previas';
    protected $description = 'Envía notificaciones por correo entre 1 y 6 horas antes de la reserva';

    public function handle()
    {
        date_default_timezone_set('America/Lima');
        $ahora = Carbon::now('America/Lima');

        // Rango de tiempo entre 1 y 6 horas antes
        $inicio = $ahora->copy()->addHour();     // +1 hora desde ahora
        $fin = $ahora->copy()->addHours(6);      // +6 horas desde ahora

        // Buscar reservas que inician entre 1h y 6h desde ahora
        $reservas = DB::table('reservas')
            ->whereBetween(DB::raw("STR_TO_DATE(CONCAT(fecha,' ',hora), '%Y-%m-%d %H:%i')"), [
                $inicio->format('Y-m-d H:i:s'),
                $fin->format('Y-m-d H:i:s')
            ])
            ->get();

        if ($reservas->isEmpty()) {
            $this->info('⏳ No hay reservas entre 1h y 6h de anticipación.');
            return 0;
        }

        foreach ($reservas as $reserva) {
            $usuario = DB::table('usuarios')->where('id_usuario', $reserva->id_usuario)->first();

            if (!$usuario || empty($usuario->correo)) {
                $this->warn("⚠️ Usuario {$reserva->id_usuario} no tiene correo registrado.");
                continue;
            }

            // Enviar el correo de recordatorio usando tu notificación existente
            try {
                // Obtener la notificación correspondiente de la BD
                $notificacionBD = DB::table('notificaciones')
                    ->where('tipo', 'previas')
                    ->first();

                // Enviar correo
                Notification::route('mail', $usuario->correo)
                    ->notify(new RecordatorioReserva(
                        (object) $reserva,   // 1. reserva
                        $notificacionBD,     // 2. mensaje desde BD
                        (object) $usuario    // 3. usuario
                    ));


                $this->info("✅ Correo enviado a {$usuario->correo}");
            } catch (\Exception $e) {
                $this->error("❌ Error al enviar correo a {$usuario->correo}: " . $e->getMessage());
            }
        }

        $this->info('🎉 Proceso completado: notificaciones enviadas.');
        return 0;
    }
}
