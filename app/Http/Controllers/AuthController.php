<?php

namespace App\Http\Controllers;

use App\Contracts\Auth\TokenIssuer;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * LOGIN
     */
    public function login(Request $request)
    {
        return $this->authenticateLogin($request, ['Cliente'], 'cliente');
    }

    public function intranetLogin(Request $request)
    {
        return $this->authenticateLogin($request, ['Empleado', 'Supervisor', 'Admin'], 'intranet');
    }

    private function authenticateLogin(Request $request, array $allowedRoles, string $accessType)
    {
        try {
            $data = $request->validate([
                'correo'   => 'required|email',
                'password' => 'required|string',
            ]);

            $correo = strtolower(trim($data['correo']));

            $selectColumns = [
                'u.id_usuario',
                'u.contrasena',
                'u.correo',
                'u.rol',
                'p.nombres',
                'p.apellidos',
            ];

            if ($this->usuariosTieneColumnasMfa()) {
                $selectColumns[] = 'u.mfa_enabled';
                $selectColumns[] = 'u.mfa_verified_at';
            }

            if ($this->usuariosTieneColumnaMfaBypass()) {
                $selectColumns[] = 'u.mfa_bypass';
            }

            // Buscar usuario con JOIN (correo en USUARIOS, datos en PERSONAS)
            $user = DB::table('usuarios as u')
                ->join('personas as p', 'u.id_persona', '=', 'p.id_persona')
                ->select($selectColumns)
                ->whereRaw('LOWER(u.correo) = ?', [$correo])
                ->first();

            if ($user && Hash::check($data['password'], $user->contrasena)) {
                // ✅ Guardar sesión en Laravel
                // El login final se completa despues de verificar MFA.

                // ✅ Solo dos dashboards para empleados y admin, clientes van al menú
                if (!in_array($user->rol, $allowedRoles, true)) {
                    return response()->json([
                        'success' => false,
                        'status_code' => 403,
                        'message' => $accessType === 'cliente'
                            ? 'Este acceso es solo para clientes. Si eres parte del equipo, ingresa por Intranet.'
                            : 'Este acceso es solo para trabajadores autorizados.',
                    ], 403);
                }

                $defaultRedirect = $this->defaultRedirectForRole($user->rol);
                $redirect = redirect()->intended($defaultRedirect)->getTargetUrl();

                if ($this->usuarioPuedeOmitirMfa($user)) {
                    Auth::loginUsingId($user->id_usuario, $request->boolean('remember'));
                    $request->session()->regenerate();

                    return response()->json([
                        'success'  => true,
                        'status_code' => 200,
                        'message'  => 'Login exitoso. MFA omitido para usuario autorizado.',
                        'redirect' => $redirect,
                        'mfa_bypassed' => true,
                        'usuario'  => [
                            'id'       => $user->id_usuario,
                            'rol'      => $user->rol,
                            'nombre'   => $user->nombres,
                            'apellido' => $user->apellidos,
                            'correo'   => $user->correo,
                        ],
                        ...$this->jwtPayloadForApi($request, (int) $user->id_usuario),
                    ]);
                }

                $mfaSetupRequired = !$this->usuarioTieneMfaActivo($user);

                return $this->startMfaChallenge($request, $user, $redirect, $request->boolean('remember'), $mfaSetupRequired);
            }

            return response()->json([
                'success' => false,
                'status_code' => 401,
                'message' => 'Credenciales incorrectas',
            ], 401);

        } catch (ValidationException $e) {
            if ($request->is('api/*')) {
                throw $e;
            }

            return response()->json([
                'success' => false,
                'status_code' => 400,
                'message' => 'Los datos enviados no son validos.',
                'errors' => $e->errors(),
            ], 400);
        } catch (\Throwable $e) {
            Log::error('Error en login', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'status_code' => 500,
                'message' => 'No se pudo iniciar sesion. Intenta nuevamente.',
            ], 500);
        }
    }

    private function defaultRedirectForRole(string $rol): string
    {
        return match ($rol) {
            'Admin' => '/admin_dashboard',
            'Empleado' => '/empleado/bandeja-reservas',
            'Supervisor' => '/empleado/panel-del-dia',
            default => '/',
        };
    }

    public function verifyMfa(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|digits:6',
        ]);

        $challenge = $request->session()->get('auth_mfa');

        if (!$challenge) {
            return response()->json([
                'success' => false,
                'status_code' => 400,
                'message' => 'No hay una verificacion MFA pendiente.',
            ], 400);
        }

        if (now()->timestamp > ($challenge['expires_at'] ?? 0)) {
            $request->session()->forget('auth_mfa');

            return response()->json([
                'success' => false,
                'status_code' => 400,
                'message' => 'El codigo MFA expiro. Vuelve a iniciar sesion.',
            ], 400);
        }

        $codeHash = $challenge['code_hash'] ?? null;

        if (!is_string($codeHash) || !Hash::check($data['code'], $codeHash)) {
            return response()->json([
                'success' => false,
                'status_code' => 400,
                'message' => 'Codigo MFA incorrecto.',
            ], 400);
        }

        $user = DB::table('usuarios as u')
            ->join('personas as p', 'u.id_persona', '=', 'p.id_persona')
            ->select(
                'u.id_usuario',
                'u.correo',
                'u.rol',
                'p.nombres',
                'p.apellidos'
            )
            ->where('u.id_usuario', $challenge['user_id'])
            ->first();

        if (!$user) {
            $request->session()->forget('auth_mfa');

            return response()->json([
                'success' => false,
                'status_code' => 404,
                'message' => 'Usuario no encontrado.',
            ], 404);
        }

        $redirect = $challenge['redirect'] ?? '/';
        $remember = (bool) ($challenge['remember'] ?? false);
        $setupRequired = (bool) ($challenge['setup_required'] ?? false);

        if ($setupRequired) {
            $this->marcarMfaComoActivo((int) $user->id_usuario);
        }

        Auth::loginUsingId($user->id_usuario, $remember);
        $request->session()->forget('auth_mfa');
        $request->session()->regenerate();

        return response()->json([
            'success'  => true,
            'status_code' => 200,
            'message'  => $setupRequired ? 'MFA creado correctamente.' : 'Verificacion MFA exitosa',
            'redirect' => $redirect,
            'mfa_created' => $setupRequired,
            'usuario'  => [
                'id'       => $user->id_usuario,
                'rol'      => $user->rol,
                'nombre'   => $user->nombres,
                'apellido' => $user->apellidos,
                'correo'   => $user->correo,
            ],
            ...$this->jwtPayloadForApi($request, (int) $user->id_usuario),
        ]);
    }

    /**
     * REGISTER
     */
    public function register(Request $request)
    {
        $request->merge([
            'correo' => strtolower(trim((string) $request->input('correo', ''))),
        ]);

        $data = $request->validate([
            'nombres'       => 'required|string|min:2',
            'apellidos'     => 'required|string|min:2',
            'tipo_doc'      => 'required|string',
            'nro_documento' => 'required|string|min:6|max:20|unique:personas,nro_documento',
            'correo'        => 'required|email|unique:usuarios,correo',
            'password'      => ['required', 'confirmed', Password::min(9)->mixedCase()->numbers()->symbols()],
        ]);
        try {
            DB::beginTransaction();

            // Insertar en PERSONAS
            $idPersona = DB::table('personas')->insertGetId([
                'nombres'             => $data['nombres'],
                'apellidos'           => $data['apellidos'],
                'tipo_doc'            => $data['tipo_doc'],
                'nro_documento'       => $data['nro_documento'],
                'estado'              => 'A',
                'fecha_creacion'      => now(),
                'fecha_actualizacion' => now(),
            ]);

            // Insertar en USUARIOS (por defecto rol Cliente)
            $usuarioData = [
                'id_persona'         => $idPersona,
                'correo'             => $data['correo'],
                'contrasena'         => Hash::make($data['password']),
                'rol'                => 'Cliente',
                'estado'             => 'A',
                'fecha_creacion'     => now(),
                'fecha_actualizacion'=> now(),
            ];

            if ($this->usuariosTieneColumnasMfa()) {
                $usuarioData['mfa_enabled'] = false;
                $usuarioData['mfa_verified_at'] = null;
            }

            $idUsuario = DB::table('usuarios')->insertGetId($usuarioData);

            DB::table('clientes')->insert([
                'id_persona' => $idPersona,
                'fecha_creacion' => now(),
                'fecha_actualizacion' => now(),
            ]);

            DB::commit();

            $user = (object) [
                'id_usuario' => $idUsuario,
                'correo' => $data['correo'],
                'rol' => 'Cliente',
                'nombres' => $data['nombres'],
                'apellidos' => $data['apellidos'],
            ];

            return $this->startMfaChallenge($request, $user, '/', false, true);

        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            Log::error('Error en registro', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'status_code' => 500,
                'message' => 'No se pudo completar el registro. Intenta nuevamente.',
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        // Elimina la sesión
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/'); // Redirige al menú principal
    }

    private function startMfaChallenge(Request $request, object $user, string $redirect, bool $remember = false, bool $setupRequired = false)
    {
        $code = (string) random_int(100000, 999999);
        $expiresMinutes = 10;

        $request->session()->put('auth_mfa', [
            'user_id' => $user->id_usuario,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes($expiresMinutes)->timestamp,
            'redirect' => $redirect,
            'remember' => $remember,
            'setup_required' => $setupRequired,
        ]);

        try {
            Mail::send(
                'emails.mfa-code',
                [
                    'code' => $code,
                    'setupRequired' => $setupRequired,
                    'expiresMinutes' => $expiresMinutes,
                    'nombre' => trim(($user->nombres ?? '') . ' ' . ($user->apellidos ?? '')),
                ],
                function ($message) use ($user, $setupRequired) {
                    $message->to($user->correo)
                        ->subject($setupRequired ? 'Crea tu MFA Pet Grooming' : 'Codigo de verificacion Pet Grooming');
                }
            );
        } catch (\Throwable $e) {
            $request->session()->forget('auth_mfa');
            Log::error('No se pudo enviar el codigo MFA', ['exception' => $e]);

            return response()->json([
                'success' => false,
                'status_code' => 500,
                'message' => 'No pudimos enviar el codigo de verificacion. Revisa la configuracion de correo.',
            ], 500);
        }

        return response()->json([
            'success' => false,
            'status_code' => 200,
            'mfa_required' => true,
            'mfa_setup_required' => $setupRequired,
            'message' => $setupRequired
                ? 'Tu cuenta aun no tiene MFA activo. Te enviamos un codigo para crearlo y proteger tu acceso.'
                : 'Te enviamos un codigo de verificacion a tu correo.',
            'masked_email' => $this->maskEmail($user->correo),
        ]);
    }

    private function maskEmail(string $email): string
    {
        [$name, $domain] = array_pad(explode('@', $email, 2), 2, '');
        $visible = substr($name, 0, 2);

        return $visible . str_repeat('*', max(strlen($name) - 2, 3)) . '@' . $domain;
    }

    private function usuariosTieneColumnasMfa(): bool
    {
        return Schema::hasColumn('usuarios', 'mfa_enabled')
            && Schema::hasColumn('usuarios', 'mfa_verified_at');
    }

    private function usuariosTieneColumnaMfaBypass(): bool
    {
        return Schema::hasColumn('usuarios', 'mfa_bypass');
    }

    private function usuarioPuedeOmitirMfa(object $user): bool
    {
        if (!$this->usuariosTieneColumnaMfaBypass()) {
            return false;
        }

        $correo = strtolower((string) $user->correo);

        if ($correo === 'admin@spa.com' && $user->rol !== 'Admin') {
            return false;
        }

        return in_array($correo, ['admin@spa.com', 'empleado@spa.com'], true)
            && (bool) ($user->mfa_bypass ?? false);
    }

    private function usuarioTieneMfaActivo(object $user): bool
    {
        if (!$this->usuariosTieneColumnasMfa()) {
            return true;
        }

        return (bool) ($user->mfa_enabled ?? false) && !empty($user->mfa_verified_at);
    }

    private function marcarMfaComoActivo(int $idUsuario): void
    {
        if (!$this->usuariosTieneColumnasMfa()) {
            return;
        }

        $data = [
            'mfa_enabled' => true,
            'mfa_verified_at' => now(),
        ];

        if (Schema::hasColumn('usuarios', 'fecha_actualizacion')) {
            $data['fecha_actualizacion'] = now();
        }

        DB::table('usuarios')
            ->where('id_usuario', $idUsuario)
            ->update($data);
    }

    private function jwtPayloadForApi(Request $request, int $idUsuario): array
    {
        if (!$request->is('api/*')) {
            return [];
        }

        $usuario = Usuario::find($idUsuario);

        if (!$usuario) {
            return [];
        }

        return app(TokenIssuer::class)->issueAccessToken($usuario);
    }

}
