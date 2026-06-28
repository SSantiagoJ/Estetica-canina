<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Resources\UsuarioResource;
use App\Services\Auth\JwtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthApiController extends Controller
{
    use ApiResponse;

    public function login(Request $request, AuthController $authController): JsonResponse
    {
        $tipoAcceso = $request->input('tipo_acceso', 'cliente');

        if ($tipoAcceso === 'intranet') {
            return $authController->intranetLogin($request);
        }

        return $authController->login($request);
    }

    public function intranetLogin(Request $request, AuthController $authController): JsonResponse
    {
        return $authController->intranetLogin($request);
    }

    public function mfa(Request $request, AuthController $authController): JsonResponse
    {
        return $authController->verifyMfa($request);
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success(
            new UsuarioResource($request->user()->load('persona')),
            'Usuario autenticado correctamente.'
        );
    }

    public function logout(Request $request, JwtService $jwt): JsonResponse
    {
        if ($token = $request->bearerToken()) {
            $jwt->revokeToken($token);
        }

        if ($request->hasSession()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return $this->success(null, 'Sesion API cerrada correctamente.');
    }
}
