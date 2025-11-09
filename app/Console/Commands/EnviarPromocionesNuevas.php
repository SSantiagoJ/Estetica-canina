<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use App\Notifications\PromocionNueva;

class EnviarPromocionesNuevas extends Command
{
    protected $signature   = 'notificaciones:promos-nuevas';
    protected $description = 'A las 08:00, envía a clientes las promociones creadas ayer con sus servicios';

    public function handle()
    {
        date_default_timezone_set('America/Lima');
        $ayer = Carbon::yesterday('America/Lima')->toDateString();

        // 1) Promos creadas AYER (estado activo si lo usas)
        $promos = DB::table('promociones as p')
            ->select('p.id_promocion','p.nombre_promocion','p.descripcion','p.descuento',
                     'p.fecha_inicio','p.fecha_fin','p.imagen_ref','p.fecha_creacion')
            ->whereDate('p.fecha_creacion', '=', $ayer)
            ->get();

        if ($promos->isEmpty()) {
            $this->info("No hay promociones creadas ayer ($ayer).");
            return 0;
        }

        // 2) Armar servicios asociados por promoción
        $promosData = [];
        foreach ($promos as $promo) {
            $servicios = DB::table('promociones_servicios as ps')
                ->join('servicios as s', 's.id_servicio', '=', 'ps.id_servicio')
                ->select('s.nombre_servicio','s.costo','s.imagen_referencial')
                ->where('ps.id_promocion', $promo->id_promocion)
                ->get()
                ->map(function($s){
                    return [
                        'nombre_servicio'    => $s->nombre_servicio,
                        'costo'              => (float)$s->costo,
                        'imagen_referencial' => $s->imagen_referencial ? (string)$s->imagen_referencial : null,
                    ];
                })->toArray();

            $promosData[] = [
                'promo' => [
                    'id_promocion'     => $promo->id_promocion,
                    'nombre_promocion' => $promo->nombre_promocion,
                    'descripcion'      => $promo->descripcion,
                    'descuento'        => $promo->descuento,
                    'fecha_inicio'     => $promo->fecha_inicio,
                    'fecha_fin'        => $promo->fecha_fin,
                    'imagen_ref'       => $promo->imagen_ref,
                ],
                'servicios' => $servicios,
            ];
        }

        // 3) Destinatarios: todos los usuarios con rol Cliente y estado A
        $clientes = DB::table('usuarios as u')
            ->join('personas as p','p.id_persona','=','u.id_persona')
            ->select('u.id_usuario','u.correo','p.nombres')
            ->where('u.rol','Cliente')
            ->where('u.estado','A')
            ->get();

        if ($clientes->isEmpty()) {
            $this->warn('No hay usuarios con rol Cliente activos.');
            return 0;
        }

        // 4) Enviar 1 correo por promo a todos los clientes
        foreach ($promosData as $bundle) {
            $promo = $bundle['promo'];
            $servs = $bundle['servicios'];

            foreach ($clientes as $cli) {
                if (empty($cli->correo)) continue;

                // Enviar correo
                Notification::route('mail', $cli->correo)
                    ->notify(new PromocionNueva($promo, $servs));
            }
            $this->info("✅ Enviada promo '{$promo['nombre_promocion']}' a " . $clientes->count() . " clientes.");
        }

        $this->info('🎉 Proceso completado.');
        return 0;
    }
}