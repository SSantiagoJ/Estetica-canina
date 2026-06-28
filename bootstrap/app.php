<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AuthenticateJwt;
use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'jwt.auth' => AuthenticateJwt::class,
            'role' => EnsureUserHasRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $exception, Request $request) {
            if (!$request->is('api/*')) {
                return null;
            }

            return response()->json([
                'success' => false,
                'status_code' => 400,
                'message' => 'Los datos enviados no son validos.',
                'errors' => $exception->errors(),
            ], 400);
        });

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if (!$request->is('api/*')) {
                return null;
            }

            return response()->json([
                'success' => false,
                'status_code' => 401,
                'message' => 'Debes iniciar sesion para acceder a este recurso.',
            ], 401);
        });

        $exceptions->render(function (ModelNotFoundException $exception, Request $request) {
            if (!$request->is('api/*')) {
                return null;
            }

            return response()->json([
                'success' => false,
                'status_code' => 404,
                'message' => 'Recurso no encontrado.',
            ], 404);
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if (!$request->is('api/*')) {
                return null;
            }

            return response()->json([
                'success' => false,
                'status_code' => 404,
                'message' => 'Ruta o recurso no encontrado.',
            ], 404);
        });

        $exceptions->render(function (Throwable $exception, Request $request) {
            if (!$request->is('api/*')) {
                return null;
            }

            if ($exception instanceof HttpExceptionInterface) {
                $status = $exception->getStatusCode();
                $message = $exception->getMessage() ?: match ($status) {
                    400 => 'Solicitud invalida.',
                    401 => 'No autenticado.',
                    403 => 'No tienes permisos para acceder a este recurso.',
                    404 => 'Recurso no encontrado.',
                    default => $status >= 500 ? 'Error interno del servidor.' : 'No se pudo procesar la solicitud.',
                };

                return response()->json([
                    'success' => false,
                    'status_code' => $status,
                    'message' => $message,
                ], $status);
            }

            return response()->json([
                'success' => false,
                'status_code' => 500,
                'message' => 'Error interno del servidor.',
            ], 500);
        });
    })->create();
