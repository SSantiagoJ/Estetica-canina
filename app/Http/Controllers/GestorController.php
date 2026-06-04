<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;
use App\Models\Reserva;
use App\Models\Usuario;
use App\Models\Mascota;
use App\Models\Servicio;

class GestorController extends Controller
{
    // Dashboard Admin
    public function index()
    {
        $reservas = Reserva::with(['mascota', 'cliente.persona', 'detalles.servicio'])->latest('id_reserva')->get();
        return view('admin_dashboard', compact('reservas'));
    }


    // Gestor de Usuarios
    public function usuarios()
    {
        $usuarios = Usuario::with('persona')->orderBy('id_usuario', 'desc')->get();
        return view('admin_usuarios', compact('usuarios'));
    }

    public function crearUsuario()
    {
        return view('admin.usuarios.create');
    }

    public function guardarUsuario(Request $request)
    {
        $request->merge([
            'correo' => strtolower(trim((string) $request->input('correo', ''))),
        ]);

        $data = $request->validate([
            'nombres' => 'required|string|min:2|max:100',
            'apellidos' => 'required|string|min:2|max:150',
            'tipo_doc' => 'required|string|max:20',
            'nro_documento' => 'required|string|min:6|max:20|unique:personas,nro_documento',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:200',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'correo' => 'required|email|max:150|unique:usuarios,correo',
            'rol' => 'required|in:Cliente,Empleado,Supervisor,Admin',
            'estado' => 'required|in:A,I',
            'puesto' => 'nullable|string|max:100',
            'password' => ['required', 'confirmed', Password::min(9)->mixedCase()->numbers()->symbols()],
        ]);

        DB::beginTransaction();

        try {
            $idPersona = DB::table('personas')->insertGetId([
                'nombres' => $data['nombres'],
                'apellidos' => $data['apellidos'],
                'tipo_doc' => $data['tipo_doc'],
                'nro_documento' => $data['nro_documento'],
                'telefono' => $data['telefono'] ?? null,
                'direccion' => $data['direccion'] ?? null,
                'fecha_nacimiento' => $data['fecha_nacimiento'] ?? null,
                'estado' => $data['estado'],
                'fecha_creacion' => now(),
                'fecha_actualizacion' => now(),
            ]);

            $usuarioData = [
                'id_persona' => $idPersona,
                'correo' => $data['correo'],
                'contrasena' => Hash::make($data['password']),
                'rol' => $data['rol'],
                'estado' => $data['estado'],
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
                $usuarioData['mfa_bypass'] = false;
            }

            DB::table('usuarios')->insert($usuarioData);

            if ($data['rol'] === 'Cliente') {
                DB::table('clientes')->insert([
                    'id_persona' => $idPersona,
                    'fecha_creacion' => now(),
                    'fecha_actualizacion' => now(),
                ]);
            } else {
                DB::table('empleados')->insert([
                    'id_persona' => $idPersona,
                    'puesto' => $data['puesto'] ?: $data['rol'],
                    'fecha_creacion' => now(),
                    'fecha_actualizacion' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('admin.usuarios')
                ->with('success', 'Usuario creado correctamente. El MFA quedara pendiente hasta su primer inicio de sesion.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'No se pudo crear el usuario. Intenta nuevamente.');
        }
    }

    // Gestor de Mascotas
    public function mascotas()
    {
        $mascotas = Mascota::with('cliente.persona')->orderBy('id_mascota', 'desc')->get();
        return view('admin_mascotas', compact('mascotas'));
    }

    // Gestor de Reservas
    public function reservas()
    {
        $reservas = Reserva::with(['mascota', 'cliente.persona', 'detalles.servicio'])
            ->latest('id_reserva')
            ->get();

        return view('admin_reservas', compact('reservas'));
    }

    // Gestor de Servicios
    public function servicios()
    {
        $servicios = Servicio::orderBy('categoria')
            ->orderBy('nombre_servicio')
            ->get()
            ->groupBy('categoria');
        
        return view('admin_servicios', compact('servicios'));
    }
      public function update(Request $request)
{
    try {
        $updates = $request->input('updates', []);
        \Log::info('Datos recibidos en update:', $updates);

        foreach ($updates as $upd) {
            $reserva = Reserva::find($upd['id']);
            if ($reserva) {
                $data = $upd['data'];

                if (isset($data['fecha'])) {
                    $reserva->fecha = $data['fecha'];
                }
                if (isset($data['hora'])) {
                    $reserva->hora = $data['hora'];
                }
                if (isset($data['estado'])) {
                    $reserva->estado = $data['estado'];
                }

                if (isset($data['mascota']) && $reserva->mascota) {
                        $reserva->mascota->nombre = $data['mascota'];
                        $reserva->mascota->save();
                    }


                $reserva->save();
            }
        }

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        \Log::error('Error en update reservas: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

    
}
