<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetSpa - Gestionar Turno</title>

    {{-- CSS principal --}}
    <link rel="stylesheet" href="{{ asset('css/gestionar_turno.css') }}">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    {{-- Removed header include --}}

    {{-- Removed sidebar and flex container --}}
    <!-- Main Content -->
    <div class="turno-main-content">
        <h1 class="turno-title">Gestionar Turno</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Botón Agregar Turno -->
        <button class="btn-agregar" onclick="abrirModal()">+ Agregar Turno</button>

        <!-- Formulario de Filtros -->
        <form action="{{ route('turnos.buscar') }}" method="GET">
            <div class="filtros">
                <div class="filtro-group">
                    <label>Empleado</label>
                    <select name="id_empleado">
                        <option value="">Todos</option>
                        @foreach($empleados as $empleado)
                            <option value="{{ $empleado->id_empleado }}">
                                {{ $empleado->persona->nombres ?? '' }} {{ $empleado->persona->apellidos ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filtro-group">
                    <label>Fecha</label>
                    <input type="date" name="fecha">
                </div>

                <div class="filtro-group">
                    <label>Estado</label>
                    <select name="estado">
                        <option value="">Todos</option>
                        <option value="A">Activo</option>
                        <option value="I">Inactivo</option>
                    </select>
                </div>

                <div class="filtro-group">
                    <button type="submit" class="btn-buscar">Buscar</button>
                </div>
            </div>
        </form>

        <!-- Tabla de Turnos -->
        <form action="{{ route('turnos.guardar-multiples') }}" method="POST">
            @csrf
            <div class="tabla-container">
                <table class="tabla-turnos">
                    <thead>
                        <tr>
                            <th>Empleado</th>
                            <th>Especialidad</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($turnos as $index => $turno)
                            <tr>
                                <td>
                                    {{ $turno->empleado->persona->nombres ?? '' }} 
                                    {{ $turno->empleado->persona->apellidos ?? '' }}
                                </td>
                                <td>{{ $turno->empleado->especialidad ?? 'N/A' }}</td>
                                <td>{{ date('d/m/Y', strtotime($turno->fecha)) }}</td>
                                <td>{{ date('H:i', strtotime($turno->hora)) }}</td>
                                <td>
                                    <input type="hidden" name="turnos[{{ $index }}][id_turno]" value="{{ $turno->id_turno }}">
                                    <select name="turnos[{{ $index }}][estado]" class="estado-select">
                                        <option value="A" {{ $turno->estado == 'A' ? 'selected' : '' }}>Activo</option>
                                        <option value="I" {{ $turno->estado == 'I' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px; color: #999;">
                                    No hay turnos registrados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($turnos->count() > 0)
                <button type="submit" class="btn-guardar">GUARDAR</button>
            @endif
        </form>
    </div>

    <!-- Modal para Agregar Turno -->
    <div id="modalAgregar" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Agregar Nuevo Turno</h2>
                <span class="close" onclick="cerrarModal()">&times;</span>
            </div>
            <form action="{{ route('turnos.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Empleado *</label>
                    <select name="id_empleado" required>
                        <option value="">Seleccione un empleado</option>
                        @foreach($empleados as $empleado)
                            <option value="{{ $empleado->id_empleado }}">
                                {{ $empleado->persona->nombres ?? '' }} {{ $empleado->persona->apellidos ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Fecha *</label>
                    <input type="date" name="fecha" required>
                </div>

                <div class="form-group">
                    <label>Hora *</label>
                    <input type="time" name="hora" required>
                </div>

                <div class="form-group">
                    <label>Estado *</label>
                    <select name="estado" required>
                        <option value="A">Activo</option>
                        <option value="I">Inactivo</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit">Guardar Turno</button>
            </form>
        </div>
    </div>

    {{-- JavaScript --}}
    <script>
        function abrirModal() {
            document.getElementById('modalAgregar').style.display = 'block';
        }

        function cerrarModal() {
            document.getElementById('modalAgregar').style.display = 'none';
        }

        // Cerrar modal al hacer clic fuera de él
        window.onclick = function(event) {
            const modal = document.getElementById('modalAgregar');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
