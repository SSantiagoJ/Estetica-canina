@extends('layouts.app')

@section('header')
    @include('partials.header')
@endsection

@section('title', 'Mi Perfil')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
@endpush

@section('content')
@php
    $mfaOmitido = strtolower((string) data_get($usuario, 'correo')) === 'admin@spa.com'
        && (bool) data_get($usuario, 'mfa_bypass');
    $mfaActivo = $mfaOmitido || ((bool) data_get($usuario, 'mfa_enabled') && filled(data_get($usuario, 'mfa_verified_at')));
    $razasPorEspecie = $razasPorEspecie ?? ['Perro' => [], 'Gato' => [], 'Otro' => []];
@endphp
<script type="application/json" id="razas-data">@json($razasPorEspecie)</script>
<div class="perfil-container">
    <section class="perfil-hero">
        <div>
            <span class="perfil-eyebrow">Familia veterinaria</span>
            <h1>Mi Perfil</h1>
            <p>Administra tus datos y mantén al día la información de tus mascotas.</p>
        </div>
        <div class="perfil-summary">
            <div class="summary-pill">
                <strong>{{ count($mascotas) }}</strong>
                <span>Mascotas</span>
            </div>
            <a href="{{ route('reservas.seleccionMascota') }}" class="summary-action">
                <i class="fas fa-calendar-plus"></i>
                <span>Reservar</span>
            </a>
        </div>
    </section>

    <section class="mfa-status-card {{ $mfaActivo ? 'mfa-status-card--active' : 'mfa-status-card--pending' }}">
        <div class="mfa-status-icon">
            <i class="fas {{ $mfaOmitido ? 'fa-user-shield' : ($mfaActivo ? 'fa-shield-heart' : 'fa-shield-halved') }}"></i>
        </div>
        <div>
            <span class="perfil-eyebrow">Seguridad de cuenta</span>
            <h2>{{ $mfaOmitido ? 'MFA omitido para admin principal' : ($mfaActivo ? 'MFA activo' : 'Crea tu MFA') }}</h2>
            <p>
                @if($mfaOmitido)
                    Esta cuenta administrativa principal puede ingresar sin codigo MFA por configuracion especial.
                @elseif($mfaActivo)
                    Tu cuenta ya tiene verificacion MFA por correo para proteger tus reservas y datos.
                @else
                    Tu cuenta aun no tiene MFA activo. En tu proximo inicio de sesion te enviaremos un codigo para crearlo y proteger tu acceso.
                @endif
            </p>
        </div>
    </section>

    <section class="perfil-card">
        <div class="perfil-content">
            <div class="perfil-avatar">
                <img src="{{ asset('images/default-avatar.png') }}" alt="Avatar de perfil">
            </div>
            <div class="perfil-info">
                <span class="perfil-label">Tutor registrado</span>
                <h2>{{ $persona->nombres }} {{ $persona->apellidos }}</h2>
                <div class="perfil-data-grid">
                    <p><i class="fas fa-id-card"></i><span><strong>DNI</strong>{{ $persona->nro_documento }}</span></p>
                    <p><i class="fas fa-envelope"></i><span><strong>Correo</strong>{{ $usuario->correo }}</span></p>
                    <p><i class="fas fa-phone"></i><span><strong>Teléfono</strong>{{ $persona->telefono ?? 'No registrado' }}</span></p>
                    <p><i class="fas fa-location-dot"></i><span><strong>Dirección</strong>{{ $persona->direccion ?? 'No registrada' }}</span></p>
                </div>
                <button class="btn-edit" type="button" onclick="openEditPerfilModal()">
                    <i class="fas fa-pen"></i> Editar perfil
                </button>
            </div>
        </div>
    </section>

    <section class="mascotas-section">
        <div class="mascotas-header">
            <div>
                <span class="perfil-eyebrow">Compañeros de cuidado</span>
                <h2>Mis Mascotas</h2>
            </div>
            <button class="btn-add-mascota" type="button" onclick="openModal()">
                <i class="fas fa-plus"></i> Añadir Mascota
            </button>
        </div>

        <div class="mascotas-grid" id="mascotasGrid">
            @forelse($mascotas as $mascota)
                @php
                    $razaImagen = strtolower(str_replace(' ', '-', $mascota->raza ?? 'default'));
                    $fotoMascota = $mascota->foto ?? asset('images/razas/' . $razaImagen . '.png');
                @endphp
                <article class="mascota-card">
                    <div class="mascota-image">
                        <img src="{{ $fotoMascota }}"
                             alt="{{ $mascota->nombre }}"
                             onerror="this.src='{{ asset('images/default-avatar.png') }}'">
                    </div>
                    <div class="mascota-info">
                        <h3>{{ $mascota->nombre }}</h3>
                        <div class="mascota-tags">
                            <span><i class="fas fa-venus-mars"></i>{{ $mascota->sexo }}</span>
                            <span><i class="fas fa-paw"></i>{{ $mascota->raza ?? 'Sin raza' }}</span>
                            <span><i class="fas fa-cake-candles"></i>{{ $mascota->edad }} años</span>
                        </div>
                        @if($mascota->descripcion)
                            <p class="mascota-description">{{ $mascota->descripcion }}</p>
                        @endif
                    </div>

                    <div class="mascota-actions">
                        <button class="btn-edit-mascota"
                            type="button"
                            onclick="openEditModal({{ $mascota->id_mascota }}, @json($mascota->nombre), @json($mascota->fecha_nacimiento), @json($mascota->sexo), @json($mascota->tamano), @json($mascota->especie), @json($mascota->raza), @json($mascota->peso), @json($mascota->descripcion))">
                            <i class="fas fa-pen"></i> Editar
                        </button>

                        <form action="{{ route('mascotas.destroy', $mascota->id_mascota) }}" method="POST"
                              onsubmit="return confirm('¿Eliminar a {{ $mascota->nombre }}? Esta acción no se puede deshacer.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete-mascota">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="no-mascotas">
                    <i class="fas fa-paw"></i>
                    <h3>Aún no tienes mascotas registradas</h3>
                    <p>Agrega a tu primera mascota para iniciar el flujo de reservas.</p>
                    <button class="btn-add-mascota" type="button" onclick="openModal()">
                        <i class="fas fa-plus"></i> Añadir Mascota
                    </button>
                </div>
            @endforelse
        </div>
    </section>

    <div id="modalMascota" class="modal">
        <div class="modal-content">
            <div class="modal-header-profile">
                <h2>Registrar Mascota</h2>
                <button type="button" class="btn-close-modal" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="formMascota">
                @csrf
                <div class="form-grid">
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
                        <input type="text" id="raza" name="raza" list="razasOptions" placeholder="Selecciona o escribe la raza" autocomplete="off">
                        <datalist id="razasOptions"></datalist>
                    </div>

                    <div class="form-group">
                        <label for="peso">Peso (kg)</label>
                        <input type="number" step="0.01" id="peso" name="peso">
                    </div>

                    <div class="form-group form-group-wide">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalEditarMascota" class="modal">
        <div class="modal-content">
            <div class="modal-header-profile">
                <h2>Editar Mascota</h2>
                <button type="button" class="btn-close-modal" onclick="closeEditModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="formEditarMascota">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_id_mascota" name="id_mascota">

                <div class="form-grid">
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
                        <input type="text" id="edit_raza" name="raza" list="editRazasOptions" placeholder="Selecciona o escribe la raza" autocomplete="off">
                        <datalist id="editRazasOptions"></datalist>
                    </div>

                    <div class="form-group">
                        <label for="edit_peso">Peso (kg)</label>
                        <input type="number" step="0.01" id="edit_peso" name="peso">
                    </div>

                    <div class="form-group form-group-wide">
                        <label for="edit_descripcion">Descripción</label>
                        <textarea id="edit_descripcion" name="descripcion" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancelar</button>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalEditarPerfil" class="modal">
        <div class="modal-content">
            <div class="modal-header-profile">
                <h2>Editar Mi Perfil</h2>
                <button type="button" class="btn-close-modal" onclick="closeEditPerfilModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="formEditarPerfil">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit_nombres">Nombres</label>
                        <input type="text" id="edit_nombres" name="nombres" value="{{ $persona->nombres }}" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_apellidos">Apellidos</label>
                        <input type="text" id="edit_apellidos" name="apellidos" value="{{ $persona->apellidos }}" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_telefono">Teléfono</label>
                        <input type="text" id="edit_telefono" name="telefono" value="{{ $persona->telefono }}">
                    </div>

                    <div class="form-group">
                        <label for="edit_direccion">Dirección</label>
                        <input type="text" id="edit_direccion" name="direccion" value="{{ $persona->direccion }}">
                    </div>

                    <div class="form-group form-group-wide">
                        <label for="edit_fecha_nacimiento_perfil">Fecha de Nacimiento</label>
                        <input type="date" id="edit_fecha_nacimiento_perfil" name="fecha_nacimiento"
                            value="{{ $persona->fecha_nacimiento }}">
                    </div>
                </div>

                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeEditPerfilModal()">Cancelar</button>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/perfil.js') }}"></script>
@endpush
