<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Models\Empleado;
use Illuminate\Http\Request;

class TurnoController extends Controller
{
    public function index()
    {
        $turnos = Turno::with('empleado.persona')->orderBy('fecha', 'desc')->get();
        $empleados = Empleado::with('persona')->get();
        
        return view('gestionar_turno', compact('turnos', 'empleados'));
    }

    public function buscar(Request $request)
    {
        $query = Turno::with('empleado.persona');

        if ($request->filled('id_empleado')) {
            $query->where('id_empleado', $request->id_empleado);
        }

        if ($request->filled('fecha')) {
            $query->where('fecha', $request->fecha);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $turnos = $query->orderBy('fecha', 'desc')->get();
        $empleados = Empleado::with('persona')->get();

        return view('gestionar_turno', compact('turnos', 'empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_empleado' => 'required',
            'fecha' => 'required|date',
            'hora' => 'required',
            'estado' => 'required',
        ]);

        Turno::create([
            'id_empleado' => $request->id_empleado,
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'estado' => $request->estado,
            'usuario_creacion' => 'admin',
            'fecha_creacion' => now(),
        ]);

        return redirect()->route('turnos.index')->with('success', 'Turno creado exitosamente');
    }

    public function guardarMultiples(Request $request)
    {
        $estados = $request->input('estados', []);

        foreach ($estados as $id_turno => $estado) {
            $turno = Turno::find($id_turno);
            if ($turno) {
                $turno->update([
                    'estado' => $estado,
                    'usuario_actualizacion' => 'admin',
                    'fecha_actualizacion' => now(),
                ]);
            }
        }

        return redirect()->route('turnos.index')->with('success', 'Estados actualizados exitosamente');
    }

    public function update(Request $request, $id)
    {
        $turno = Turno::findOrFail($id);
        
        $turno->update([
            'estado' => $request->estado,
            'usuario_actualizacion' => 'admin',
            'fecha_actualizacion' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $turno = Turno::findOrFail($id);
        $turno->delete();

        return redirect()->route('turnos.index')->with('success', 'Turno eliminado exitosamente');
    }
}