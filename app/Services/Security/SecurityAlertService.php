<?php

namespace App\Services\Security;

use App\Contracts\Security\SecurityAlertReporter;
use App\Models\Notificacion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SecurityAlertService implements SecurityAlertReporter
{
    public function reportUnauthorizedApiAccess(Request $request, string $reason, ?Usuario $actor = null): void
    {
        if (!$this->canStoreNotifications()) {
            return;
        }

        try {
            $message = $this->buildMessage($request, $reason, $actor);

            Usuario::query()
                ->where('rol', 'Admin')
                ->where('estado', 'A')
                ->pluck('id_usuario')
                ->each(function ($adminId) use ($message): void {
                    $this->createIfNotDuplicated((int) $adminId, $message);
                });
        } catch (Throwable) {
            // Las alertas de seguridad no deben bloquear la respuesta API.
        }
    }

    private function createIfNotDuplicated(int $adminId, string $message): void
    {
        $exists = Notificacion::query()
            ->where('id_usuario', $adminId)
            ->where('tipo', 'Seguridad')
            ->where('mensaje', $message)
            ->where('fecha_creacion', '>=', now()->subMinutes(10))
            ->exists();

        if ($exists) {
            return;
        }

        Notificacion::create([
            'id_usuario' => $adminId,
            'tipo' => 'Seguridad',
            'mensaje' => $message,
            'fecha_envio' => now(),
            'estado' => 'A',
            'usuario_creacion' => 'sistema-seguridad',
            'fecha_creacion' => now(),
            'usuario_actualizacion' => 'sistema-seguridad',
            'fecha_actualizacion' => now(),
        ]);
    }

    private function buildMessage(Request $request, string $reason, ?Usuario $actor): string
    {
        $actorText = $actor
            ? "usuario {$actor->correo} ({$actor->rol})"
            : 'usuario no autenticado';

        return substr(sprintf(
            'Alerta API: %s. Ruta %s %s desde IP %s por %s.',
            $reason,
            $request->method(),
            $request->path(),
            $request->ip() ?: 'desconocida',
            $actorText
        ), 0, 500);
    }

    private function canStoreNotifications(): bool
    {
        return Schema::hasTable('notificaciones')
            && Schema::hasTable('usuarios')
            && Schema::hasColumn('notificaciones', 'tipo')
            && Schema::hasColumn('notificaciones', 'mensaje')
            && Schema::hasColumn('usuarios', 'rol');
    }
}
