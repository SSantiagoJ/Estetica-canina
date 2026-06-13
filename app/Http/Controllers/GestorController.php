<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use App\Models\Reserva;
use App\Models\Usuario;
use App\Models\Mascota;
use App\Models\RazaImagen;
use App\Models\Servicio;

class GestorController extends Controller
{
    // Dashboard Admin
    public function index()
    {
        $reservas = Reserva::with(['mascota', 'cliente.persona', 'detalles.servicio'])->latest('id_reserva')->get();
        return view('admin.dashboard', compact('reservas'));
    }


    // Gestor de Usuarios
    public function usuarios()
    {
        $usuarios = Usuario::with('persona')->orderBy('id_usuario', 'desc')->get();
        return view('admin.usuarios.index', compact('usuarios'));
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
            Log::error('No se pudo crear usuario desde admin', [
                'exception' => $e,
                'correo' => $data['correo'] ?? null,
                'rol' => $data['rol'] ?? null,
            ]);

            return back()
                ->withInput()
                ->with('error', 'No se pudo crear el usuario. Revisa que el rol exista en la base de datos y que los campos no esten duplicados.');
        }
    }

    // Gestor de Mascotas
    public function mascotas()
    {
        $mascotas = Mascota::with('cliente.persona')->orderBy('id_mascota', 'desc')->get();
        $razaImagenes = RazaImagen::orderBy('especie')
            ->orderBy('raza')
            ->get();

        return view('admin.mascotas.index', compact('mascotas', 'razaImagenes'));
    }

    public function guardarFotoRaza(Request $request)
    {
        $data = $request->validate([
            'especie' => 'required|string|in:Perro,Gato,Otro',
            'raza' => 'required|string|min:2|max:120',
            'imagen' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:51200',
        ], [
            'imagen.max' => 'La imagen no debe superar los 50 MB.',
            'imagen.image' => 'El archivo debe ser una imagen valida.',
            'imagen.mimes' => 'Usa una imagen JPG, JPEG, PNG, WEBP o GIF.',
        ]);

        $especie = RazaImagen::normalizarEspecie($data['especie']);
        $raza = trim($data['raza']);
        $slug = RazaImagen::crearSlugRaza($raza);
        $archivo = $request->file('imagen');
        $extension = strtolower($archivo->getClientOriginalExtension() ?: $archivo->extension());
        $nombreArchivo = Str::slug($especie) . '-' . $slug . '-' . now()->format('YmdHis') . '.' . $extension;
        $path = $archivo->storeAs('razas', $nombreArchivo, 'public');

        $imagen = RazaImagen::where('especie', $especie)
            ->where('slug', $slug)
            ->first();

        if ($imagen && $imagen->imagen_path) {
            Storage::disk('public')->delete($imagen->imagen_path);
        }

        RazaImagen::updateOrCreate(
            [
                'especie' => $especie,
                'slug' => $slug,
            ],
            [
                'raza' => $raza,
                'imagen_path' => $path,
                'tamano_bytes' => $archivo->getSize(),
                'mime_type' => $archivo->getMimeType(),
                'estado' => 'A',
                'usuario_creacion' => auth()->user()->correo ?? null,
                'usuario_actualizacion' => auth()->user()->correo ?? null,
            ]
        );

        return redirect()
            ->route('admin.mascotas')
            ->with('success', 'Foto de raza guardada correctamente. Se aceptan imagenes de hasta 50 MB.');
    }

    public function eliminarFotoRaza(RazaImagen $razaImagen)
    {
        if ($razaImagen->imagen_path) {
            Storage::disk('public')->delete($razaImagen->imagen_path);
        }

        $razaImagen->delete();

        return redirect()
            ->route('admin.mascotas')
            ->with('success', 'Foto de raza eliminada correctamente.');
    }

    public function mostrarFotoRaza(RazaImagen $razaImagen)
    {
        if ($razaImagen->estado !== 'A' || blank($razaImagen->imagen_path)) {
            abort(404);
        }

        if (!Storage::disk('public')->exists($razaImagen->imagen_path)) {
            abort(404);
        }

        $path = Storage::disk('public')->path($razaImagen->imagen_path);
        $mimeType = $razaImagen->mime_type ?: Storage::disk('public')->mimeType($razaImagen->imagen_path);

        return response()->file($path, [
            'Content-Type' => $mimeType ?: 'image/jpeg',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    // Gestor de Reservas
    public function reservas()
    {
        $reservas = Reserva::with(['mascota', 'cliente.persona', 'detalles.servicio'])
            ->latest('id_reserva')
            ->get();

        return view('admin.reservas.index', compact('reservas'));
    }

    // Gestor de Servicios
    public function servicios()
    {
        $servicios = Servicio::orderBy('categoria')
            ->orderBy('nombre_servicio')
            ->get()
            ->groupBy('categoria');
        
        return view('admin.servicios.index', compact('servicios'));
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
