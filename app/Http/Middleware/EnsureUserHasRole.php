<?php

namespace App\Http\Middleware;

use App\Contracts\Security\SecurityAlertReporter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function __construct(private readonly SecurityAlertReporter $securityAlerts)
    {
    }

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                abort(401, 'Debes iniciar sesion.');
            }

            $loginRoute = $request->is('admin*') || $request->is('empleado*') || $request->is('admin_dashboard') || $request->is('intranet*')
                ? 'intranet.login'
                : 'login';

            return redirect()->route($loginRoute);
        }

        $allowedRoles = array_map('trim', $roles);

        if (!in_array($user->rol, $allowedRoles, true)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $this->securityAlerts->reportUnauthorizedApiAccess(
                    $request,
                    'token valido sin permisos para el recurso',
                    $user
                );

                abort(403, 'No tienes permisos para acceder a esta seccion.');
            }

            abort(403, 'No tienes permisos para acceder a esta seccion.');
        }

        return $next($request);
    }
}
