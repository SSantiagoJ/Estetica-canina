@section('header')
    @include('partials.header')
@endsection

@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
@section('content')
    <div class="perfil-container">
        <!-- Sección de Perfil -->
        <div class="perfil-card">
            <h2>Mi Perfil</h2>
            <div class="perfil-content">
                <div class="perfil-avatar">
                    <img src="{{ asset('images/default-avatar.png') }}" alt="Avatar">
                </div>
                <div class="perfil-info">
                    <h3>Bienvenido, {{ $persona->nombres }}</h3>
                    <p><strong>DNI:</strong> {{ $persona->nro_documento }}</p>
                    <p><strong>Correo:</strong> {{ $usuario->correo }}</p>
                    <p><strong>Teléfono:</strong> {{ $persona->telefono ?? 'No registrado' }}</p>
                    <p><strong>Dirección:</strong> {{ $persona->direccion ?? 'No registrada' }}</p>
                    <button class="btn-edit" onclick="openEditPerfilModal()">✏️ Editar</button>
                </div>
            </div>
        </div>

        <!-- Sección de Mascotas -->
        <div class="mascotas-section">
            <div class="mascotas-header">
                <h2>Mis Mascotas</h2>
                <button class="btn-add-mascota" onclick="openModal()">
                    🐾 Añadir Mascota
                </button>
            </div>

            <div class="mascotas-grid" id="mascotasGrid">
                @forelse($mascotas as $mascota)
                    <div class="mascota-card">
                        <div class="mascota-image">
                            <img src="{{ asset('images/default-pet.png') }}">
                        </div>
                        <div class="mascota-info">
                            <h3><strong>{{ $mascota->nombre }}</strong></h3>
                            <p>{{ $mascota->sexo == 'Macho' ? '♂️' : '♀️' }} {{ $mascota->sexo }}</p>
                            <p>🐕 {{ $mascota->raza ?? 'Sin raza' }}</p>
                            <p>🎂 {{ $mascota->edad }} años</p>
                        </div>

                        <div style="margin-top:12px; display:flex; gap:8px;">
                            <button class="btn-edit-mascota"
                                onclick="openEditModal({{ $mascota->id_mascota }}, '{{ $mascota->nombre }}', '{{ $mascota->fecha_nacimiento }}', '{{ $mascota->sexo }}', '{{ $mascota->tamano }}', '{{ $mascota->especie }}', '{{ $mascota->raza }}', '{{ $mascota->peso }}', '{{ $mascota->descripcion }}')">
                                ✏️ Editar
                            </button>

                            <!-- Formulario para eliminar -->
                            <form action="{{ route('mascotas.destroy', $mascota->id_mascota) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar a {{ $mascota->nombre }}? Esta acción no se puede deshacer.');"
                                  style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete-mascota">🗑️ Eliminar</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="no-mascotas">No tienes mascotas registradas aún.</p>
                @endforelse
            </div>
        </div>

        <!-- Modal para Registrar Mascota -->
        <div id="modalMascota" class="modal">
            <div class="modal-content">
                <h2>Registrar Mascota</h2>
                <form id="formMascota">
                    @csrf
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>

                    <div class="form-group">
                        <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
                    </div>

                    <div class="form-group">
                        <label for="sexo">Sexo</label>
                        <select id="sexo" name="sexo" required>
                            <option value="">Seleccionar</option>
                            <option value="Macho">Macho</option>
                            <option value="Hembra">Hembra</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tamano">Tamaño</label>
                        <select id="tamano" name="tamano">
                            <option value="">Seleccionar</option>
                            <option value="Pequeño">Pequeño</option>
                            <option value="Mediano">Mediano</option>
                            <option value="Grande">Grande</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="especie">Especie</label>
                        <select id="especie" name="especie" required>
                            <option value="">Seleccionar</option>
                            <option value="Perro">Perro</option>
                            <option value="Gato">Gato</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="raza">Raza</label>
                        <input type="text" id="raza" name="raza">
                    </div>

                    <div class="form-group">
                        <label for="peso">Peso (kg)</label>
                        <input type="number" step="0.01" id="peso" name="peso">
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>

                    <div class="modal-buttons">
                        <button type="button" class="btn-cancel" onclick="closeModal()">Cancelar</button>
                        <button type="submit" class="btn-save">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal para Editar Mascota -->
        <div id="modalEditarMascota" class="modal">
            <div class="modal-content">
                <h2>Editar Mascota</h2>
                <form id="formEditarMascota">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_id_mascota" name="id_mascota">

                    <div class="form-group">
                        <label for="edit_nombre">Nombre</label>
                        <input type="text" id="edit_nombre" name="nombre" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_fecha_nacimiento">Fecha de Nacimiento</label>
                        <input type="date" id="edit_fecha_nacimiento" name="fecha_nacimiento" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_sexo">Sexo</label>
                        <select id="edit_sexo" name="sexo" required>
                            <option value="">Seleccionar</option>
                            <option value="Macho">Macho</option>
                            <option value="Hembra">Hembra</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_tamano">Tamaño</label>
                        <select id="edit_tamano" name="tamano">
                            <option value="">Seleccionar</option>
                            <option value="Pequeño">Pequeño</option>
                            <option value="Mediano">Mediano</option>
                            <option value="Grande">Grande</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_especie">Especie</label>
                        <select id="edit_especie" name="especie" required>
                            <option value="">Seleccionar</option>
                            <option value="Perro">Perro</option>
                            <option value="Gato">Gato</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_raza">Raza</label>
                        <input type="text" id="edit_raza" name="raza">
                    </div>

                    <div class="form-group">
                        <label for="edit_peso">Peso (kg)</label>
                        <input type="number" step="0.01" id="edit_peso" name="peso">
                    </div>

                    <div class="form-group">
                        <label for="edit_descripcion">Descripción</label>
                        <textarea id="edit_descripcion" name="descripcion" rows="3"></textarea>
                    </div>

                    <div class="modal-buttons">
                        <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancelar</button>
                        <button type="submit" class="btn-save">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal para Editar Perfil -->
        <div id="modalEditarPerfil" class="modal">
            <div class="modal-content">
                <h2>Editar Mi Perfil</h2>
                <form id="formEditarPerfil">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="edit_nombres">Nombres</label>
                        <input type="text" id="edit_nombres" name="nombres" value="{{ $persona->nombres }}" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_apellidos">Apellidos</label>
                        <input type="text" id="edit_apellidos" name="apellidos" value="{{ $persona->apellidos }}" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_telefono">Telefono</label>
                        <input type="text" id="edit_telefono" name="telefono" value="{{ $persona->telefono }}">
                    </div>

                    <div class="form-group">
                        <label for="edit_direccion">Direccion</label>
                        <input type="text" id="edit_direccion" name="direccion" value="{{ $persona->direccion }}">
                    </div>

                    <div class="form-group">
                        <label for="edit_fecha_nacimiento">Fecha de Nacimiento</label>
                        <input type="date" id="edit_fecha_nacimiento" name="fecha_nacimiento"
                            value="{{ $persona->fecha_nacimiento }}">
                    </div>

                    <div class="modal-buttons">
                        <button type="button" class="btn-cancel" onclick="closeEditPerfilModal()">Cancelar</button>
                        <button type="submit" class="btn-save">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>

@endsection

    @section('scripts')
        <script src="{{ asset('js/perfil.js') }}"></script>
    @endsection