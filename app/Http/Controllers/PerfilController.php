<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mascota;
use App\Models\Cliente;

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

        return view('perfil', compact('usuario', 'persona', 'mascotas'));
    }

    public function storeMascota(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|string',
            'raza' => 'nullable|string|max:50',
            'tamano' => 'nullable|string|max:20',
            'especie' => 'required|string|max:50',
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
            return response()->json(['error' => 'Cliente no encontrado'], 404);
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
            'raza' => 'nullable|string|max:50',
            'tamano' => 'nullable|string|max:20',
            'especie' => 'required|string|max:50',
            'peso' => 'nullable|numeric',
            'descripcion' => 'nullable|string|max:500'
        ]);

        $usuario = Auth::user();
        $mascota = Mascota::find($id);

        if (!$mascota) {
            return response()->json(['error' => 'Mascota no encontrada'], 404);
        }

        // Verificar que la mascota pertenece al cliente del usuario actual
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