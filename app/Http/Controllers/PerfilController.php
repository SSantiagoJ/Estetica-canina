<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Mascota;
use App\Models\Cliente;
use App\Models\RazaImagen;

class PerfilController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();

        if (!$usuario->persona) {
            return redirect('/dashboard')->with('error', 'No se encontró información de perfil');
        }

        $persona = $usuario->persona;

        $cliente = Cliente::where('id_persona', $persona->id_persona)->first();

        $mascotas = [];
        if ($cliente) {
            $mascotas = Mascota::where('id_cliente', $cliente->id_cliente)->get();
        }

        $razasDisponibles = Mascota::whereNotNull('raza')
            ->where('raza', '<>', '')
            ->select('especie', 'raza')
            ->distinct()
            ->get();

        if (Schema::hasTable('raza_imagenes')) {
            $razasConFoto = RazaImagen::where('estado', 'A')
                ->select('especie', 'raza')
                ->get();

            $razasDisponibles = $razasDisponibles->merge($razasConFoto);
        }

        $razasPorEspecie = array_merge(
            ['Perro' => [], 'Gato' => [], 'Otro' => []],
            $razasDisponibles
                ->map(function ($item) {
                    return [
                        'especie' => RazaImagen::normalizarEspecie($item->especie),
                        'raza' => trim((string) $item->raza),
                    ];
                })
                ->filter(fn ($item) => filled($item['raza']))
                ->groupBy('especie')
                ->map(fn ($items) => $items->pluck('raza')->unique()->sort()->values()->all())
                ->toArray()
        );

        return view('cliente.perfil', compact('usuario', 'persona', 'mascotas', 'razasPorEspecie'));
    }

    public function storeMascota(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|string',
            'raza' => 'nullable|string|max:120',
            'tamano' => 'nullable|string|max:20',
            'especie' => 'required|string|in:Perro,Gato,Otro',
            'peso' => 'nullable|numeric',
            'descripcion' => 'nullable|string|max:500'
        ]);

        $usuario = Auth::user();

        if (!$usuario->persona) {
            return response()->json(['error' => 'No se encontró información de perfil'], 404);
        }

        $persona = $usuario->persona;
        $cliente = Cliente::where('id_persona', $persona->id_persona)->first();

        if (!$cliente) {
            $cliente = new Cliente();
            $cliente->id_persona = $persona->id_persona;
            $cliente->fecha_creacion = now();
            $cliente->save();
        }

        $mascota = new Mascota();
        $mascota->nombre = $request->nombre;
        $mascota->fecha_nacimiento = $request->fecha_nacimiento;
        $mascota->sexo = $request->sexo;
        $mascota->raza = $request->raza;
        $mascota->tamano = $request->tamano;
        $mascota->especie = $request->especie;
        $mascota->peso = $request->peso;
        $mascota->descripcion = $request->descripcion;
        $mascota->id_cliente = $cliente->id_cliente;
        $mascota->usuario_creacion = $usuario->correo;
        $mascota->save();

        return response()->json(['success' => true, 'mascota' => $mascota]);
    }

    public function updateMascota(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|string',
            'raza' => 'nullable|string|max:120',
            'tamano' => 'nullable|string|max:20',
            'especie' => 'required|string|in:Perro,Gato,Otro',
            'peso' => 'nullable|numeric',
            'descripcion' => 'nullable|string|max:500'
        ]);

        $usuario = Auth::user();
        $mascota = Mascota::find($id);

        if (!$mascota) {
            return response()->json(['error' => 'Mascota no encontrada'], 404);
        }

        $persona = $usuario->persona;
        $cliente = Cliente::where('id_persona', $persona->id_persona)->first();

        if (!$cliente || $mascota->id_cliente != $cliente->id_cliente) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $mascota->nombre = $request->nombre;
        $mascota->fecha_nacimiento = $request->fecha_nacimiento;
        $mascota->sexo = $request->sexo;
        $mascota->raza = $request->raza;
        $mascota->tamano = $request->tamano;
        $mascota->especie = $request->especie;
        $mascota->peso = $request->peso;
        $mascota->descripcion = $request->descripcion;
        $mascota->usuario_actualizacion = $usuario->correo;
        $mascota->fecha_actualizacion = now();
        $mascota->save();

        return response()->json(['success' => true, 'mascota' => $mascota]);
    }

    public function updatePerfil(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:200',
            'fecha_nacimiento' => 'nullable|date'
        ]);

        $usuario = Auth::user();

        if (!$usuario->persona) {
            return response()->json(['error' => 'No se encontro informacion de perfil'], 404);
        }

        $persona = $usuario->persona;
        $persona->nombres = $request->nombres;
        $persona->apellidos = $request->apellidos;
        $persona->telefono = $request->telefono;
        $persona->direccion = $request->direccion;
        $persona->fecha_nacimiento = $request->fecha_nacimiento;
        $persona->fecha_actualizacion = now();
        $persona->save();

        return response()->json(['success' => true, 'persona' => $persona]);
    }

    public function intranetPerfil()
    {
        $usuario = Auth::user();

        if (!$usuario || !$usuario->persona) {
            return redirect()->route('intranet.login')
                ->with('error', 'No se encontro informacion de perfil.');
        }

        $persona = $usuario->persona;
        $empleado = $usuario->empleado;

        return view('intranet.perfil', compact('usuario', 'persona', 'empleado'));
    }

    public function updateIntranetPerfil(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'nullable|string|max:150',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:200',
            'fecha_nacimiento' => 'nullable|date'
        ]);

        $usuario = Auth::user();

        if (!$usuario || !$usuario->persona) {
            return redirect()->route('intranet.perfil')
                ->with('error', 'No se encontro informacion de perfil.');
        }

        $persona = $usuario->persona;
        $persona->nombres = $request->nombres;

        if (Schema::hasColumn('personas', 'apellidos')) {
            $persona->apellidos = $request->apellidos;
        } else {
            $partesApellido = preg_split('/\s+/', trim((string) $request->apellidos), 2);

            if (Schema::hasColumn('personas', 'apellido_paterno')) {
                $persona->apellido_paterno = $partesApellido[0] ?? null;
            }

            if (Schema::hasColumn('personas', 'apellido_materno')) {
                $persona->apellido_materno = $partesApellido[1] ?? null;
            }
        }

        foreach (['telefono', 'direccion', 'fecha_nacimiento'] as $campo) {
            if (Schema::hasColumn('personas', $campo)) {
                $persona->{$campo} = $request->{$campo};
            }
        }

        if (Schema::hasColumn('personas', 'usuario_actualizacion')) {
            $persona->usuario_actualizacion = $usuario->correo;
        }

        if (Schema::hasColumn('personas', 'fecha_actualizacion')) {
            $persona->fecha_actualizacion = now();
        }

        $persona->save();

        return redirect()->route('intranet.perfil')
            ->with('success', 'Perfil actualizado correctamente.');
    }

    public function destroy($id)
    {
        $usuario = Auth::user();
        $mascota = Mascota::findOrFail($id);

        // verificar propietario
        $persona = $usuario->persona;
        $cliente = Cliente::where('id_persona', $persona->id_persona)->first();

        if (!$cliente || $mascota->id_cliente != $cliente->id_cliente) {
            return redirect()->back()->with('error', 'No autorizado');
        }

        $mascota->delete();
        return redirect()->back()->with('success', 'Mascota eliminada correctamente.');
    }
}
