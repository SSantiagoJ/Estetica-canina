<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UsuarioResource;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UsuarioApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $usuarios = Usuario::with('persona')
            ->when($request->filled('rol'), fn ($query) => $query->where('rol', $request->input('rol')))
            ->when($request->filled('estado'), fn ($query) => $query->where('estado', $request->input('estado')))
            ->when($request->filled('buscar'), function ($query) use ($request) {
                $buscar = '%' . $request->input('buscar') . '%';

                $query->where(function ($subQuery) use ($buscar) {
                    $subQuery->where('correo', 'like', $buscar)
                        ->orWhereHas('persona', function ($personaQuery) use ($buscar) {
                            $personaQuery->where('nombres', 'like', $buscar)
                                ->orWhere('apellidos', 'like', $buscar)
                                ->orWhere('nro_documento', 'like', $buscar);
                        });
                });
            })
            ->latest('id_usuario')
            ->paginate((int) $request->input('per_page', 15));

        return UsuarioResource::collection($usuarios);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombres' => ['required', 'string', 'min:2', 'max:100'],
            'apellidos' => ['required', 'string', 'min:2', 'max:150'],
            'tipo_doc' => ['required', 'string', 'max:20'],
            'nro_documento' => ['required', 'string', 'min:6', 'max:20', 'unique:personas,nro_documento'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'fecha_nacimiento' => ['nullable', 'date', 'before:today'],
            'correo' => ['required', 'email', 'max:150', 'unique:usuarios,correo'],
            'rol' => ['required', 'in:Cliente,Empleado,Supervisor,Admin'],
            'estado' => ['nullable', 'in:A,I'],
            'puesto' => ['nullable', 'string', 'max:100'],
            'mfa_bypass' => ['nullable', 'boolean'],
            'password' => ['required', 'confirmed', Password::min(9)->mixedCase()->numbers()->symbols()],
        ]);

        $usuario = DB::transaction(function () use ($data) {
            $idPersona = DB::table('personas')->insertGetId([
                'nombres' => $data['nombres'],
                'apellidos' => $data['apellidos'],
                'tipo_doc' => $data['tipo_doc'],
                'nro_documento' => $data['nro_documento'],
                'telefono' => $data['telefono'] ?? null,
                'direccion' => $data['direccion'] ?? null,
                'fecha_nacimiento' => $data['fecha_nacimiento'] ?? null,
                'estado' => $data['estado'] ?? 'A',
                'fecha_creacion' => now(),
                'fecha_actualizacion' => now(),
            ]);

            $usuarioData = [
                'id_persona' => $idPersona,
                'correo' => strtolower(trim($data['correo'])),
                'contrasena' => Hash::make($data['password']),
                'rol' => $data['rol'],
                'estado' => $data['estado'] ?? 'A',
                'fecha_creacion' => now(),
                'fecha_actualizacion' => now(),
            ];

            if (Schema::hasColumn('usuarios', 'mfa_enabled')) {
                $usuarioData['mfa_enabled'] = false;
            }

            if (Schema::hasColumn('usuarios', 'mfa_verified_at')) {
                $usuarioData['mfa_verified_at'] = null;
            }

            if (Schema::hasColumn('usuarios', 'mfa_bypass')) {
                $usuarioData['mfa_bypass'] = (bool) ($data['mfa_bypass'] ?? false);
            }

            $idUsuario = DB::table('usuarios')->insertGetId($usuarioData);

            $this->crearFichaRol($idPersona, $data['rol'], $data['puesto'] ?? null);

            return Usuario::with('persona')->findOrFail($idUsuario);
        });

        return $this->success(new UsuarioResource($usuario), 'Usuario creado correctamente.', 201);
    }

    public function show(Usuario $usuario): UsuarioResource
    {
        return new UsuarioResource($usuario->load('persona'));
    }

    public function update(Request $request, Usuario $usuario): JsonResponse
    {
        $data = $request->validate([
            'nombres' => ['sometimes', 'string', 'min:2', 'max:100'],
            'apellidos' => ['sometimes', 'string', 'min:2', 'max:150'],
            'tipo_doc' => ['sometimes', 'string', 'max:20'],
            'nro_documento' => ['sometimes', 'string', 'min:6', 'max:20', Rule::unique('personas', 'nro_documento')->ignore($usuario->id_persona, 'id_persona')],
            'telefono' => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:200'],
            'fecha_nacimiento' => ['nullable', 'date', 'before:today'],
            'correo' => ['sometimes', 'email', 'max:150', Rule::unique('usuarios', 'correo')->ignore($usuario->id_usuario, 'id_usuario')],
            'rol' => ['sometimes', 'in:Cliente,Empleado,Supervisor,Admin'],
            'estado' => ['sometimes', 'in:A,I'],
            'puesto' => ['nullable', 'string', 'max:100'],
            'mfa_bypass' => ['nullable', 'boolean'],
            'password' => ['nullable', 'confirmed', Password::min(9)->mixedCase()->numbers()->symbols()],
        ]);

        DB::transaction(function () use ($data, $usuario) {
            $personaData = array_intersect_key($data, array_flip([
                'nombres',
                'apellidos',
                'tipo_doc',
                'nro_documento',
                'telefono',
                'direccion',
                'fecha_nacimiento',
            ]));

            if (!empty($personaData)) {
                $personaData['fecha_actualizacion'] = now();
                DB::table('personas')
                    ->where('id_persona', $usuario->id_persona)
                    ->update($personaData);
            }

            $usuarioData = array_intersect_key($data, array_flip([
                'correo',
                'rol',
                'estado',
                'mfa_bypass',
            ]));

            if (isset($usuarioData['correo'])) {
                $usuarioData['correo'] = strtolower(trim($usuarioData['correo']));
            }

            if (array_key_exists('password', $data) && filled($data['password'])) {
                $usuarioData['contrasena'] = Hash::make($data['password']);
            }

            if (!empty($usuarioData)) {
                $usuarioData['fecha_actualizacion'] = now();
                DB::table('usuarios')
                    ->where('id_usuario', $usuario->id_usuario)
                    ->update($usuarioData);
            }

            if (isset($data['rol'])) {
                $this->crearFichaRol($usuario->id_persona, $data['rol'], $data['puesto'] ?? null);
            }
        });

        return $this->success(new UsuarioResource($usuario->fresh()->load('persona')), 'Usuario actualizado correctamente.');
    }

    public function destroy(Usuario $usuario): JsonResponse
    {
        $usuario->estado = 'I';
        $usuario->fecha_actualizacion = now();
        $usuario->save();

        return $this->success(new UsuarioResource($usuario->fresh()->load('persona')), 'Usuario desactivado correctamente.');
    }

    private function crearFichaRol(int $idPersona, string $rol, ?string $puesto): void
    {
        if ($rol === 'Cliente') {
            if (!DB::table('clientes')->where('id_persona', $idPersona)->exists()) {
                DB::table('clientes')->insert([
                    'id_persona' => $idPersona,
                    'fecha_creacion' => now(),
                    'fecha_actualizacion' => now(),
                ]);
            }

            return;
        }

        if (!DB::table('empleados')->where('id_persona', $idPersona)->exists()) {
            DB::table('empleados')->insert([
                'id_persona' => $idPersona,
                'puesto' => $puesto ?: $rol,
                'fecha_creacion' => now(),
                'fecha_actualizacion' => now(),
            ]);
        }
    }
}
