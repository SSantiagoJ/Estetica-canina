<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * LOGIN
     */
    public function login(Request $request)
    {
        try {
            $data = $request->validate([
                'correo'   => 'required|email',
                'password' => 'required|min:6',
            ]);

            // Buscar usuario con JOIN (correo en USUARIOS, datos en PERSONAS)
            $user = DB::table('usuarios as u')
                ->join('personas as p', 'u.id_persona', '=', 'p.id_persona')
                ->select(
                    'u.id_usuario',
                    'u.contrasena',
                    'u.correo',
                    'u.rol',
                    'p.nombres',
                    'p.apellidos'
                )
                ->where('u.correo', $data['correo'])
                ->first();

            if ($user && Hash::check($data['password'], $user->contrasena)) {
                // ✅ Guardar sesión en Laravel
                Auth::loginUsingId($user->id_usuario);

                // ✅ Solo dos dashboards
                $redirect = match ($user->rol) {
                     'Admin'    => '/admin_dashboard',
                    'Empleado' => '/Empleado/bandeja-reservas',
                    default             => '/dashboard',
                };

                return response()->json([
                    'success'  => true,
                    'message'  => 'Login exitoso',
                    'redirect' => $redirect,
                    'usuario'  => [
                        'id'       => $user->id_usuario,
                        'rol'      => $user->rol,
                        'nombre'   => $user->nombres,
                        'apellido' => $user->apellidos,
                        'correo'   => $user->correo,
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * REGISTER
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'nombres'       => 'required|string|min:2',
            'apellidos'     => 'required|string|min:2',
            'tipo_doc'      => 'required|string',
            'nro_documento' => 'required|string|min:6|max:20|unique:personas,nro_documento',
            'correo'        => 'required|email|unique:usuarios,correo',
            'password'      => 'required|min:6|confirmed',
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
            $idUsuario = DB::table('usuarios')->insertGetId([
                'id_persona'         => $idPersona,
                'correo'             => $data['correo'],
                'contrasena'         => Hash::make($data['password']),
                'rol'                => 'Cliente',
                'estado'             => 'A',
                'fecha_creacion'     => now(),
                'fecha_actualizacion'=> now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registro exitoso',
                'usuario' => [
                    'id'       => $idUsuario,
                    'nombre'   => $data['nombres'],
                    'apellido' => $data['apellidos'],
                    'correo'   => $data['correo'],
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error en el registro: ' . $e->getMessage(),
            ]);
        }
    }
    public function logout(Request $request)
{
    // Elimina la sesión
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login'); // Redirige al login
}

}
