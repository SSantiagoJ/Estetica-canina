<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            if ($request->expectsJson()) {
                abort(401, 'Debes iniciar sesion.');
            }

            $loginRoute = $request->is('admin*') || $request->is('empleado*') || $request->is('admin_dashboard') || $request->is('intranet*')
                ? 'intranet.login'
                : 'login';

            return redirect()->route($loginRoute);
        }

        $allowedRoles = array_map('trim', $roles);

        if (!in_array($user->rol, $allowedRoles, true)) {
            abort(403, 'No tienes permisos para acceder a esta seccion.');
        }

        return $next($request);
    }
}
