<?php

namespace App\Http\Middleware;

use App\Contracts\Auth\TokenIssuer;
use App\Contracts\Security\SecurityAlertReporter;
use App\Models\Usuario;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthenticateJwt
{
    public function __construct(
        private readonly TokenIssuer $jwt,
        private readonly SecurityAlertReporter $securityAlerts
    )
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            $this->securityAlerts->reportUnauthorizedApiAccess($request, 'acceso sin token Bearer');

            return $this->unauthorized('Debes enviar un token Bearer para acceder a este recurso.');
        }

        try {
            $payload = $this->jwt->decode($token);
        } catch (Throwable) {
            $this->securityAlerts->reportUnauthorizedApiAccess($request, 'token JWT invalido');

            return $this->unauthorized('Token JWT invalido, expirado o revocado.');
        }

        $usuario = Usuario::with(['persona', 'empleado'])
            ->where('id_usuario', (int) $payload['sub'])
            ->where('estado', 'A')
            ->first();

        if (!$usuario) {
            $this->securityAlerts->reportUnauthorizedApiAccess($request, 'token asociado a usuario inexistente o inactivo');

            return $this->unauthorized('El usuario del token no existe o esta inactivo.');
        }

        $request->attributes->set('jwt_payload', $payload);
        $request->attributes->set('jwt_token', $token);
        $request->setUserResolver(fn () => $usuario);
        Auth::setUser($usuario);

        return $next($request);
    }

    private function unauthorized(string $message): Response
    {
        return response()->json([
            'success' => false,
            'status_code' => 401,
            'message' => $message,
        ], 401);
    }
}
